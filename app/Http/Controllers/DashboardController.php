<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plot;
use App\Models\User;
use App\Models\ProductionSummary;
use App\Models\ProductionYear;
use App\Services\DSSService;
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
        $query = ProductionSummary::with('plot');

        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }

        $summaries = $query->orderBy('created_at', 'asc')->get();

        // 2. KPI Calculations
        $totalWeight = $summaries->sum('dry_rubber_weight_kg');
        $totalIncome = $summaries->sum('total_amount_baht');
        $totalFarmers = User::where('role', 'farmer')->count();
        $totalPlots = Plot::count();

        $topPlot = $summaries->sortByDesc('dry_rubber_weight_kg')->first();
        if ($topPlot) {
            $topPlot->contribution_percent = $totalWeight > 0
                ? round(($topPlot->dry_rubber_weight_kg / $totalWeight) * 100, 2)
                : 0;
        }

        // 3. Weather & Date
        $today = now();
        $day = $today->translatedFormat('l');
        $date = $today->translatedFormat('d F Y');

        $city = 'Krabi,TH';
        $apiKey = env('OPENWEATHER_API_KEY', '');
        $temperature = 28;
        $condition = 'Clear';
        $icon = '☀️';

        if (!empty($apiKey)) {
            try {
                $weatherResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]);
                if ($weatherResponse->successful()) {
                    $wData = $weatherResponse->json();
                    $temperature = round($wData['main']['temp']);
                    $condition = $wData['weather'][0]['main'];
                    $icon = $this->getWeatherIcon($condition);
                }
            } catch (\Exception $e) {
                // Silently fail to default values
            }
        }

        // 4. Quality Index Calculation
        $qualityIndex = $summaries->avg('quality_index') ?? 75;

        // 5. Monthly DSS Recommendation
        $monthlyDSS = [];
        $monthlyGroups = $summaries->groupBy(fn($item) => $item->created_at->format('M'));

        foreach ($monthlyGroups as $monthName => $rows) {
            $avgWeight = $rows->avg('dry_rubber_weight_kg');
            $score = min(10, round(($avgWeight / 500) * 10));

            $monthlyDSS[] = [
                'month' => $monthName,
                'score' => $score,
                'recommendation' => match (true) {
                    $score >= 7 => 'Optimal Harvest',
                    $score >= 4 => 'Monitor Conditions',
                    default => 'High Risk of Washout'
                }
            ];
        }

        // 6. Chart Data (Latex Production Trend)
        $chartLabels = $summaries->pluck('created_at')->map(fn($d) => $d->format('d M'))->values();
        $chartData = [
            [
                'label' => 'Dry Rubber (kg)',
                'data' => $summaries->pluck('dry_rubber_weight_kg')->toArray(),
                'borderColor' => '#16a34a',
                'backgroundColor' => 'rgba(22, 163, 74, 0.1)',
                'tension' => 0.4,
                'fill' => true
            ]
        ];

        // 7. Quality Trend Data (For the second chart)
        $qualityLabels = $chartLabels;
        $qualityData = $summaries->pluck('quality_index')->toArray();

        // 8. Top Contributors
        $topContributors = User::where('role', 'farmer')
            ->withSum('productionSummaries as total_latex', 'dry_rubber_weight_kg')
            ->orderByDesc('total_latex')
            ->take(5)
            ->get();

        return view($viewName, compact(
            'totalWeight',
            'totalFarmers',
            'totalPlots',
            'topPlot',
            'qualityIndex',
            'day',
            'date',
            'temperature',
            'condition',
            'icon',
            'monthlyDSS',
            'chartLabels',
            'chartData',
            'qualityLabels',
            'qualityData',
            'topContributors'
        ));
    }

    private function getWeatherIcon($condition)
    {
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