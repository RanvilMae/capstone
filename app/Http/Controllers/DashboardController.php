<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plot;
use App\Models\User;
use App\Models\ProductionSummary;
use App\Models\ProductionYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
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

    /**
     * Shared logic to prevent code duplication and handle localization
     */
    private function generateDashboardData(Request $request, $viewName)
    {
        $plots = Plot::with('farmer')->get();
        $productionYears = ProductionYear::orderBy('start_date')->get();
        $query = ProductionSummary::with('plot');

        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }

        if ($request->filled('production_year_id')) {
            $year = ProductionYear::find($request->production_year_id);
            if ($year) {
                $query->whereBetween('created_at', [$year->start_date, $year->end_date]);
            }
        }

        $summaries = $query->get();

        /* ===== KPI ===== */
        $totalWeight = $summaries->sum('dry_rubber_weight_kg');
        $totalIncome = $summaries->sum('total_amount_baht');
        $totalFarmers = User::where('role', 'farmer')->count();
        $totalPlots = Plot::count();

        $topPlot = $summaries->sortByDesc('dry_rubber_weight_kg')->first();
        if ($topPlot) {
            $topPlot->contribution_percent = round(($topPlot->dry_rubber_weight_kg / max($totalWeight, 1)) * 100, 2);
        }

        /* ===== CHART DATA ===== */
        $chartLabels = $summaries->pluck('plot.plot_location')->unique();
        $chartData = [];
        foreach ($chartLabels as $label) {
            $plotSummary = $summaries->where('plot.plot_location', $label);
            $chartData[] = [
                'label' => $label,
                'data' => $plotSummary->pluck('dry_rubber_weight_kg')->toArray(),
                'borderColor' => '#' . substr(md5($label), 0, 6),
                'backgroundColor' => 'rgba(34,197,94,0.2)',
                'fill' => true,
                'tension' => 0.4
            ];
        }

        $qualityIndex = 75;

        /* ===== MONTHLY DSS (Localized Months) ===== */
        $monthlyDSS = [];
        $monthlySummaries = $summaries->groupBy(fn($item) => $item->created_at->format('m'));

        foreach ($monthlySummaries as $monthNum => $rows) {
            $totalWeightMonth = $rows->sum('dry_rubber_weight_kg');
            $productionScore = min(40, ($totalWeightMonth / 1000) * 40);
            $finalScore = round($productionScore + 60); // simplified logic

            $monthlyDSS[] = [
                // translatedFormat('F') gives 'มกราคม' for Thai and 'January' for English
                'month' => Carbon::create()->month((int)$monthNum)->translatedFormat('F'),
                'score' => $finalScore,
                'recommendation' => match (true) {
                    $finalScore >= 75 => 'Optimal Harvest',
                    $finalScore >= 50 => 'Monitor Conditions',
                    default => 'High Risk'
                }
            ];
        }

        /* ===== TOP CONTRIBUTORS ===== */
        $topContributors = Plot::with('farmer')
            ->join('production_summaries', 'plots.id', '=', 'production_summaries.plot_id')
            ->selectRaw('farmer_id, SUM(dry_rubber_weight_kg) as total_latex')
            ->groupBy('farmer_id')
            ->orderByDesc('total_latex')
            ->take(5)
            ->get();

        /* ===== MONTHLY QUALITY TREND (Localized) ===== */
        $qualityGroups = $summaries->groupBy(fn($item) => $item->created_at->translatedFormat('F'));
        $qualityLabels = $qualityGroups->keys();
        $qualityData = $qualityGroups->map(fn($items) => round($items->avg('quality_index') ?? 75, 2))->values();

        /* ===== WEATHER & LOCALIZED DATE ===== */
        $today = now();
        $day = $today->translatedFormat('l'); // 'วันพฤหัสบดี' or 'Thursday'
        $date = $today->translatedFormat('d F Y'); // '12 กุมภาพันธ์ 2026'

        // Weather Logic
        $city = 'Bangkok,TH';
        $apiKey = env('OPENWEATHER_API_KEY', '');
        $temperature = 28;
        $condition = 'Sunny';
        $icon = '☀️';

        if (!empty($apiKey)) {
            try {
                $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city, 'appid' => $apiKey, 'units' => 'metric',
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    $temperature = round($data['main']['temp']);
                    $condition = $data['weather'][0]['main'];
                    $icon = $this->getWeatherIcon($condition);
                }
            } catch (\Exception $e) {}
        }

        return view($viewName, compact(
            'totalWeight', 'totalIncome', 'totalFarmers', 'totalPlots', 'topPlot',
            'chartLabels', 'chartData', 'qualityIndex', 'monthlyDSS',
            'day', 'date', 'temperature', 'condition', 'icon',
            'topContributors', 'qualityLabels', 'qualityData'
        ));
    }

    private function getWeatherIcon($condition) {
        return match (strtolower($condition)) {
            'clear' => '☀️',
            'clouds' => '☁️',
            'rain' => '🌧️',
            'drizzle' => '🌦️',
            'thunderstorm' => '⛈️',
            'mist', 'fog', 'haze' => '🌫️',
            default => '🌤️',
        };
    }
}