<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Farm;
use App\Models\User;
use App\Models\WeatherData;
use App\Models\MarketPrice;
use App\Models\Intervention;
use App\Models\Plot;
use App\Models\ProductionSummary;
use App\Models\ProductionYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        return $user->role === 'admin'
            ? $this->adminDashboard($request)
            : $this->farmerDashboard();
    }

    /* =======================
        ADMIN DASHBOARD
    ========================*/
    public function adminDashboard(Request $request)
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
                $query->whereBetween('created_at', [
                    $year->start_date,
                    $year->end_date
                ]);
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
            $topPlot->contribution_percent = round(
                ($topPlot->dry_rubber_weight_kg / max($totalWeight, 1)) * 100,
                2
            );
        }

        /* ===== CHART DATA ===== */
        $chartLabels = $summaries->pluck('plot.plot_location');
        $chartData = $summaries->pluck('dry_rubber_weight_kg');

        // Fake quality index for now (can be DSS later)
        $qualityIndex = 75;

        $monthlyDSS = [];

        $monthlySummaries = ProductionSummary::selectRaw('
            MONTH(created_at) as month,
            SUM(dry_rubber_weight_kg) as total_weight,
            AVG(total_amount_baht) as avg_income
        ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        foreach ($monthlySummaries as $row) {

            /* ---- PRODUCTION SCORE (0–40) ---- */
            $productionScore = min(40, ($row->total_weight / 1000) * 40);

            /* ---- WEATHER SCORE (0–30) ---- */
            $weather = WeatherData::whereMonth('date', $row->month)->avg('rainfall_mm');

            if ($weather <= 10)
                $weatherScore = 30;       // ideal
            elseif ($weather <= 20)
                $weatherScore = 20;
            else
                $weatherScore = 10;                      // heavy rain

            /* ---- PRICE SCORE (0–30) ---- */
            $price = MarketPrice::whereMonth('date', $row->month)->avg('price_per_kg');

            if ($price >= 70)
                $priceScore = 30;
            elseif ($price >= 50)
                $priceScore = 20;
            else
                $priceScore = 10;

            /* ---- FINAL DSS SCORE ---- */
            $finalScore = round($productionScore + $weatherScore + $priceScore);

            $monthlyDSS[] = [
                'month' => Carbon::create()->month($row->month)->format('F'),
                'score' => $finalScore,
                'recommendation' => match (true) {
                    $finalScore >= 75 => '✅ Optimal Harvest',
                    $finalScore >= 50 => '⚠ Monitor Conditions',
                    default => '❌ High Risk'
                }
            ];
        }

        $today = now();
        $day = $today->format('l'); // Monday
        $date = $today->format('d F Y'); //12 January 2026

        $city = 'Bangkok,TH';
        $apiKey = env('OPENWEATHER_API_KEY', ''); // fallback empty
        $units = 'metric';

        // Default weather values in case API fails
        $temperature = 28;  // default temp
        $condition = 'Sunny';
        $icon = '☀️';

        if (!empty($apiKey)) {
            try {
                $weatherData = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => $units,
                ]);

                $data = $weatherData->json();

                if (isset($data['main']['temp']) && isset($data['weather'][0]['main'])) {
                    $temperature = round($data['main']['temp']);
                    $condition = $data['weather'][0]['main'];

                    // Map condition to emoji
                    $icon = match (strtolower($condition)) {
                        'CLear' => '☀️',
                        'Cloudy' => '☁️',
                        'Rainy' => '🌧️',
                        'Drizzle' => '🌦️',
                        'Thunderstorm' => '⛈️',
                        'Snow' => '❄️',
                        'Mist', 'Fog', 'haze' => '🌫️',
                        default => '🌤️',
                    };
                }
            } catch (\Exception $e) {
                // API failed → keep default values
            }
        }

        return view('dashboard.admin', compact(
            'totalWeight',
            'totalIncome',
            'totalFarmers',
            'totalPlots',
            'topPlot',
            'chartLabels',
            'chartData',
            'qualityIndex',
            'monthlyDSS',
            'day',
            'date',
            'temperature',
            'condition',
            'icon'
        ));



    }

    /* =======================
        FARMER DASHBOARD
    ========================*/
    public function farmerDashboard()
    {
        $userId = Auth::id();

        $year = ProductionYear::latest()->first();

        $summaries = ProductionSummary::with('plot')
            ->whereHas('plot', fn($q) => $q->where('farmer_id', $userId))
            ->where('production_year_id', $year->id)
            ->get();

        $totalWeight = $summaries->sum('dry_rubber_weight_kg');
        $totalIncome = $summaries->sum('total_amount_baht');
        $topPlot = $summaries->sortByDesc('dry_rubber_weight_kg')->first();

        return view('dashboard.farmer', compact(
            'summaries',
            'totalWeight',
            'totalIncome',
            'topPlot'
        ));
    }
}
