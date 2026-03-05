<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plot;
use App\Models\User;
use App\Models\LatexTransaction;
use App\Services\DSSService; 
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use App\Imports\LatexProductionImport;

class DashboardController extends Controller
{
    protected $dss;

    public function __construct(DSSService $dss)
    {
        $this->dss = $dss;
    }

    public function index(Request $request) { return $this->generateDashboardData($request, 'dashboard.admin'); }
    public function adminDashboard(Request $request) { return $this->generateDashboardData($request, 'dashboard.admin'); }
    public function staffDashboard(Request $request) { return $this->generateDashboardData($request, 'dashboard.staff'); }

    private function generateDashboardData(Request $request, $viewName)
    {
        // 1. Data Fetching
        $query = LatexTransaction::with('plot');
        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }
        $allTransactions = $query->orderBy('transaction_date', 'asc')->get();

        // 2. KPI Calculations
        $totalWeight = $allTransactions->sum('dry_rubber_weight_kg');
        $totalIncome = $allTransactions->sum('total_amount'); 
        $totalFarmers = User::where('role', 'farmer')->count();
        $totalPlots = max(1, Plot::count());
        $qualityIndex = round($allTransactions->avg('dry_rubber_content') ?? 75, 1);

        $overallAvg = LatexTransaction::avg('dry_rubber_weight_kg') ?? 0;
        $currentAvg = $allTransactions->avg('dry_rubber_weight_kg') ?? 0;
        $growthRate = ($overallAvg > 0) ? round((($currentAvg - $overallAvg) / $overallAvg) * 100, 1) : 0;

        // 3. REAL-TIME WEATHER INITIALIZATION
        $now = now();
        $day = $now->translatedFormat('l');
        $date = $now->translatedFormat('d F Y');
        $temperature = 28; 
        $condition = 'Clear';
        $icon = '☀️';
        $outlook = collect();
        $dssScore = 10; 

        $apiKey = env('OPENWEATHER_API_KEY');

        if ($apiKey) {
            try {
                $response = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                    'q' => 'Krabi,TH',
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                if ($response->successful()) {
                    $weatherData = $response->json();
                    $current = $weatherData['list'][0];
                    $temperature = round($current['main']['temp']);
                    $condition = $current['weather'][0]['main'];
                    $icon = $this->getWeatherIcon($condition);

                    // Decision Support Score (Current)
                    $res = $this->dss->getRecommendation(
                        $current['rain']['3h'] ?? 0, 
                        $temperature, 
                        $current['main']['humidity'] ?? 0, 
                        $current['wind']['speed'] ?? 0
                    );
                    $dssScore = $res['score'];

                    // 7-period outlook
                    $outlook = collect($weatherData['list'])
                        ->filter(fn($i) => str_contains($i['dt_txt'], '12:00:00'))
                        ->take(7)
                        ->map(function ($item) {
                            $r = $this->dss->getRecommendation(
                                $item['rain']['3h'] ?? 0, 
                                $item['main']['temp'], 
                                $item['main']['humidity'] ?? 0, 
                                $item['wind']['speed'] ?? 0
                            );
                            
                            return array_merge($r, [
                                'day' => Carbon::parse($item['dt_txt'])->translatedFormat('D'),
                                'temp' => round($item['main']['temp']),
                                'rain' => $item['rain']['3h'] ?? 0,
                                'humidity' => $item['main']['humidity'] ?? 0,
                                'wind' => $item['wind']['speed'] ?? 0,
                            ]);
                        });
                }
            } catch (\Exception $e) { 
                \Log::error("Weather API Error: " . $e->getMessage()); 
            }
        }

        // 4. CHART & CORRELATION LOGIC
        $correlationScore = 0; // Initialize to avoid ErrorException
        $yieldWarning = null;
        $chartLabels = []; $productionData = []; $rainfallData = []; $monthlyDSS = [];
        $krabiRainfallBaseline = ['01'=>35, '02'=>40, '03'=>85, '04'=>160, '05'=>260, '06'=>230, '07'=>280, '08'=>320, '09'=>360, '10'=>310, '11'=>190, '12'=>70];

        $monthlyGroups = $allTransactions->groupBy(fn($item) => Carbon::parse($item->transaction_date)->format('Y-m'));

        foreach ($monthlyGroups as $key => $rows) {
            $dateObj = Carbon::parse($key);
            $chartLabels[] = $dateObj->format('M Y');
            $yield = $rows->sum('dry_rubber_weight_kg') / $totalPlots;
            $productionData[] = round($yield, 2);
            
            $monthNum = $dateObj->format('m');
            $rainfallData[] = $krabiRainfallBaseline[$monthNum] ?? 0;

            $analysis = $this->dss->getMonthlyRecommendation($yield, $rows->avg('dry_rubber_content'));
            $monthlyDSS[] = [
                'month' => $dateObj->format('F Y'),
                'score' => $analysis['score'],
                'recommendation' => $analysis['recommendation']
            ];
        }

        // Statistical Correlation (Pearson r)
        if (($count = count($productionData)) > 1) {
            $meanX = array_sum($rainfallData) / $count;
            $meanY = array_sum($productionData) / $count;
            $num = 0; $divX = 0; $divY = 0;

            for ($i = 0; $i < $count; $i++) {
                $dX = $rainfallData[$i] - $meanX;
                $dY = $productionData[$i] - $meanY;
                $num += ($dX * $dY);
                $divX += pow($dX, 2);
                $divY += pow($dY, 2);
            }
            $denom = sqrt($divX * $divY);
            $correlationScore = ($denom != 0) ? ($num / $denom) : 0;
        }

        // 5. ANOMALY DETECTION & ADVICE
        if ($correlationScore < -0.6 && $currentAvg < $overallAvg) {
            $yieldWarning = [
                'title' => __('Yield Anomaly Detected'),
                'message' => __('Production trends are deviating from weather expectations. Potential stress detected.'),
            ];
        }

        $correlationStrength = match(true) {
            $correlationScore > 0.3 => __('Positive Correlation'),
            $correlationScore < -0.3 => __('Negative Correlation (Washout)'),
            default => __('No Direct Correlation'),
        };

        $userAdvice = match(true) {
            $correlationScore > 0.5 => "Rainfall is currently beneficial for latex flow.",
            $correlationScore < -0.5 => "High risk of 'Washout'—latex yields are dropping significantly.",
            default => "Production is currently stable relative to rainfall patterns.",
        };

        $monthlyDSS = array_slice(array_reverse($monthlyDSS), 0, 12);
        $topContributors = User::where('role', 'farmer')
            ->withSum('latexTransactions as total_latex', 'dry_rubber_weight_kg')
            ->orderByDesc('total_latex')
            ->take(5)
            ->get();

        return view($viewName, compact(
            'totalWeight', 'totalIncome', 'totalFarmers', 'totalPlots', 'growthRate', 'qualityIndex',
            'chartLabels', 'productionData', 'rainfallData', 'monthlyDSS', 'dssScore',
            'day', 'date', 'temperature', 'condition', 'icon', 'outlook',
            'topContributors', 'correlationScore', 'correlationStrength', 'userAdvice', 'yieldWarning'
        ));
    }

    private function getWeatherIcon($condition) {
        return match (strtolower($condition)) {
            'clear' => '☀️', 'clouds' => '☁️', 'rain' => '🌧️', 'drizzle' => '🌦️', 'thunderstorm' => '⛈️', default => '🌤️',
        };
    }

    public function uploadExcel(Request $request) {
        set_time_limit(600);
        $request->validate(['excel_file' => 'required|mimes:xlsx,xls,csv|max:128000']);
        try {
            Excel::import(new LatexProductionImport, $request->file('excel_file'));
            return back()->with('success', 'Data Integrated Successfully.');
        } catch (\Exception $e) { return back()->with('error', 'Import failed: ' . $e->getMessage()); }
    }
}