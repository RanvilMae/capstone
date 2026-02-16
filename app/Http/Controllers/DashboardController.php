<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plot;
use App\Models\User;
use App\Models\ProductionSummary;
use App\Models\ProductionYear;
use App\Services\DSSService; // Ensure you created this service
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    protected $dss;

    public function __construct(DSSService $dss)
    {
        $this->dss = $dss;
    }

    public function index(Request $request)
    {
        return $this->generateDashboardData($request, 'dashboard.admin');
    }

    public function adminDashboard(Request $request)
    {
        return $this->generateDashboardData($request, 'dashboard.admin');
    }

    public function staffDashboard(Request $request)
    {
        return $this->generateDashboardData($request, 'dashboard.staff');
    }

    private function generateDashboardData(Request $request, $viewName)
    {
        // 1. Data Fetching
        $plots = Plot::with('farmer')->get();
        $productionYears = ProductionYear::orderBy('start_date')->get();
        $query = ProductionSummary::with('plot');

        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }

        $summaries = $query->get();

        // 2. KPI Calculations with Historical Comparison
        $totalWeight = $summaries->sum('dry_rubber_weight_kg');
        $totalIncome = $summaries->sum('total_amount_baht');
        $totalFarmers = User::where('role', 'farmer')->count();
        $totalPlots = Plot::count();

        // DSS Feature: Growth vs Historical Average (Simulated 5-year baseline)
        $historicalAverage = 5000; // This should ideally be a query from past years
        $growthRate = $historicalAverage > 0 
            ? round((($totalWeight - $historicalAverage) / $historicalAverage) * 100, 1) 
            : 0;

        $topPlot = $summaries->sortByDesc('dry_rubber_weight_kg')->first();
        if ($topPlot) {
            $topPlot->contribution_percent = round(($topPlot->dry_rubber_weight_kg / max($totalWeight, 1)) * 100, 2);
        }

        // 3. Weather & Localized Date
        $today = now();
        $day = $today->translatedFormat('l');
        $date = $today->translatedFormat('d F Y');

        // Weather API Integration
        $city = 'Krabi,TH'; // Updated to Krabi per your documentation
        $apiKey = env('OPENWEATHER_API_KEY', '');
        $temperature = 28;
        $condition = 'Clear';
        $icon = '☀️';
        $rainfallData = [0, 0, 5, 12, 0, 2, 0]; // Simulated weekly rain for the chart

        if (!empty($apiKey)) {
            try {
                $weatherResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city, 'appid' => $apiKey, 'units' => 'metric',
                ]);
                if ($weatherResponse->successful()) {
                    $wData = $weatherResponse->json();
                    $temperature = round($wData['main']['temp']);
                    $condition = $wData['weather'][0]['main'];
                    $icon = $this->getWeatherIcon($condition);
                }
            } catch (\Exception $e) {}
        }

        // 4. DSS Feature: 7-Day Harvesting Outlook
        // In a real scenario, you'd fetch 'forecast' from the API. Here we simulate:
        $outlook = collect([
            ['day' => 'Mon', 'temp' => 27, 'rain' => 0],
            ['day' => 'Tue', 'temp' => 26, 'rain' => 1.2],
            ['day' => 'Wed', 'temp' => 24, 'rain' => 15.5],
            ['day' => 'Thu', 'temp' => 28, 'rain' => 0],
            ['day' => 'Fri', 'temp' => 29, 'rain' => 0],
            ['day' => 'Sat', 'temp' => 27, 'rain' => 4.5],
            ['day' => 'Sun', 'temp' => 26, 'rain' => 0],
        ])->map(function($item) {
            $rec = $this->dss->getRecommendation($item['rain'], $item['temp']);
            return array_merge($item, $rec);
        });

        // Current Tapping Score for the Header
        $currentRain = 0; // Should be from API
        $currentDSS = $this->dss->getRecommendation($currentRain, $temperature);
        $dssScore = $currentDSS['score'];
        
        $qualityIndex = $summaries->avg('quality_index') ?? 75;
        // 5. Monthly DSS Recommendation Logic
        $monthlyDSS = [];
        $monthlySummaries = $summaries->groupBy(fn($item) => $item->created_at->format('m'));

        foreach ($monthlySummaries as $monthNum => $rows) {
            $avgWeight = $rows->avg('dry_rubber_weight_kg');
            $score = min(10, round(($avgWeight / 500) * 10)); // Example score logic
            
            $monthlyDSS[] = [
                'month' => Carbon::create()->month((int)$monthNum)->translatedFormat('F'),
                'score' => $score,
                'recommendation' => match (true) {
                    $score >= 7 => 'Optimal Harvest',
                    $score >= 4 => 'Monitor Conditions',
                    default => 'High Risk of Washout'
                }
            ];
        }

        // 6. Chart Data
        $chartLabels = $summaries->pluck('created_at')->map(fn($d) => $d->format('d M'))->unique()->values();
        $productionData = $summaries->pluck('dry_rubber_weight_kg')->toArray();

        // 7. Top Contributors
        $topContributors = User::where('role', 'farmer')
            ->withSum('productionSummaries as total_latex', 'dry_rubber_weight_kg')
            ->orderByDesc('total_latex')
            ->take(5)
            ->get();

        return view($viewName, compact(
            'totalWeight', 'totalIncome', 'totalFarmers', 'totalPlots', 'topPlot', 'growthRate',
            'chartLabels', 'productionData', 'rainfallData', 'monthlyDSS', 'dssScore',
            'day', 'date', 'temperature', 'condition', 'icon', 'outlook',
            'topContributors', 'qualityIndex'
        ));
    }

    private function getWeatherIcon($condition) {
        return match (strtolower($condition)) {
            'clear' => '☀️',
            'clouds' => '☁️',
            'rain' => '🌧️',
            'drizzle' => '🌦️',
            'thunderstorm' => '⛈️',
            default => '🌤️',
        };
    }
}