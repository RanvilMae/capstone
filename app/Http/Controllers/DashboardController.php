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
use Illuminate\Support\Facades\DB;
use App\Imports\LatexProductionImport;
use App\Exports\FreshRubberSalesReportExport;

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
        // 1. Data Fetching & Filtering
        $query = LatexTransaction::with('plot');
        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }
        $allTransactions = $query->orderBy('transaction_date', 'asc')->get();

        // Paginated transactions table for detailed view
        $recentTransactions = (clone $query)->latest('transaction_date')->paginate(15);

        // 2. Core KPI & Metric Calculations
        $totalWeight = $allTransactions->sum('dry_rubber_weight_kg');
        $totalVolume = $allTransactions->sum('volume_kg');
        $totalIncome = $allTransactions->sum('total_amount'); 
        $totalFarmers = User::where('role', 'farmer')->count();
        $totalPlots = max(1, Plot::count());
        $qualityIndex = round($allTransactions->avg('dry_rubber_content') ?? 75, 1);

        $overallAvg = LatexTransaction::avg('dry_rubber_weight_kg') ?? 0;
        $currentAvg = $allTransactions->avg('dry_rubber_weight_kg') ?? 0;
        $growthRate = ($overallAvg > 0) ? round((($currentAvg - $overallAvg) / $overallAvg) * 100, 1) : 0;

        // 3. Weather Integration & 7-Day Forecast Outlook (Open-Meteo API)
        $now = now();
        $day = $now->translatedFormat('l');
        $date = $now->translatedFormat('d F Y');
        $temperature = 28; 
        $condition = 'Clear';
        $icon = '☀️';
        $outlook = collect();
        $dssScore = 10; 

        try {
            // Krabi, Thailand Coordinates: Lat 8.0863, Lon 98.9063
            $response = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => 8.0863,
                'longitude' => 98.9063,
                'current' => 'temperature_2m,relative_humidity_2m,precipitation,weather_code,wind_speed_10m',
                'daily' => 'temperature_2m_max,precipitation_sum,relative_humidity_2m_mean,wind_speed_10m_max',
                'timezone' => 'Asia/Bangkok'
            ]);

            if ($response->successful()) {
                $weatherData = $response->json();
                
                // Current Weather Processing
                $current = $weatherData['current'] ?? [];
                $temperature = round($current['temperature_2m'] ?? 28);
                $weatherCode = $current['weather_code'] ?? 0;
                
                $condition = $this->mapMeteoCodeToCondition($weatherCode);
                $icon = $this->getWeatherIcon($condition);

                // Current DSS Score
                $res = $this->dss->getRecommendation(
                    $current['precipitation'] ?? 0, 
                    $temperature, 
                    $current['relative_humidity_2m'] ?? 0, 
                    $current['wind_speed_10m'] ?? 0
                );
                $dssScore = $res['score'];

                // 7-Day Daily Forecast Outlook
                $daily = $weatherData['daily'] ?? [];
                if (!empty($daily['time'])) {
                    $outlook = collect($daily['time'])->map(function ($dateString, $index) use ($daily) {
                        $temp = $daily['temperature_2m_max'][$index] ?? 28;
                        $rain = $daily['precipitation_sum'][$index] ?? 0;
                        $humidity = $daily['relative_humidity_2m_mean'][$index] ?? 0;
                        $wind = $daily['wind_speed_10m_max'][$index] ?? 0;

                        // DSS Recommendation per day
                        $r = $this->dss->getRecommendation($rain, $temp, $humidity, $wind);

                        return array_merge($r, [
                            'day' => Carbon::parse($dateString)->translatedFormat('D'),
                            'temp' => round($temp),
                            'rain' => $rain,
                            'humidity' => $humidity,
                            'wind' => $wind,
                        ]);
                    })->take(7);
                }
            }
        } catch (\Exception $e) { 
            \Log::error("Open-Meteo Weather API Error: " . $e->getMessage()); 
        }

        // 4. Chart & Statistical Correlation Logic
        $correlationScore = 0;
        $yieldWarning = null;
        $chartLabels = []; 
        $productionData = []; 
        $rainfallData = []; 
        $monthlyDSS = [];
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

        // 5. Anomaly Detection & Advice
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
        
        // Top farmer contributors
        $topContributors = User::where('role', 'farmer')
            ->withSum('latexTransactions as total_latex', 'dry_rubber_weight_kg')
            ->orderByDesc('total_latex')
            ->take(5)
            ->get();

        return view($viewName, compact(
            'totalWeight', 'totalVolume', 'totalIncome', 'totalFarmers', 'totalPlots', 'growthRate', 'qualityIndex',
            'recentTransactions', 'chartLabels', 'productionData', 'rainfallData', 'monthlyDSS', 'dssScore',
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
        } catch (\Exception $e) { 
            return back()->with('error', 'Import failed: ' . $e->getMessage()); 
        }
    }

    private function mapMeteoCodeToCondition($code) {
        return match (true) {
            $code === 0 => 'Clear',
            in_array($code, [1, 2, 3]) => 'Clouds',
            in_array($code, [51, 53, 55, 56, 57]) => 'Drizzle',
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82]) => 'Rain',
            in_array($code, [95, 96, 99]) => 'Thunderstorm',
            default => 'Clear',
        };
    }

    public function reportsIndex(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $quarter = $request->input('quarter', 1);
        $semester = $request->input('semester', 1);

        $query = LatexTransaction::with(['plot', 'plot.farmer']);

        switch ($type) {
            case 'monthly':
                $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
                $periodLabel = Carbon::createFromDate($year, $month, 1)->format('F Y');
                break;
            case 'quarterly':
                $startMonth = ($quarter - 1) * 3 + 1;
                $query->whereYear('transaction_date', $year)->whereBetween(\DB::raw('MONTH(transaction_date)'), [$startMonth, $startMonth + 2]);
                $periodLabel = "Q{$quarter} {$year}";
                break;
            case 'semestral':
                $startMonth = $semester == 1 ? 1 : 7;
                $query->whereYear('transaction_date', $year)->whereBetween(\DB::raw('MONTH(transaction_date)'), [$startMonth, $startMonth + 5]);
                $periodLabel = "Semester {$semester}, {$year}";
                break;
            case 'yearly':
                $query->whereYear('transaction_date', $year);
                $periodLabel = "Year {$year}";
                break;
            case 'daily':
            default:
                $query->whereDate('transaction_date', $date);
                $periodLabel = Carbon::parse($date)->format('l, F j, Y');
                break;
        }

        $transactions = $query->get();
        $avgPrice = $transactions->avg('price_per_kg') ?? 39.50;

        return view('reports.index', compact(
            'transactions', 'type', 'date', 'month', 'year', 'quarter', 'semester', 'periodLabel', 'avgPrice'
        ));
    }

    /**
     * Download Excel File
     */
    public function exportFreshRubberReport(Request $request)
    {
        $filters = $request->only(['type', 'date', 'month', 'year', 'quarter', 'semester']);
        $filename = 'Fresh_Rubber_Sales_Report_' . ($filters['type'] ?? 'daily') . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new FreshRubberSalesReportExport($filters), $filename);
    }
}