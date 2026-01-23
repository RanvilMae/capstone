<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LatexTransaction;
use App\Models\Plot;
use App\Models\ProductionYear;
use App\Models\MarketPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $plots = Plot::with('farmer')->get();
        $years = ProductionYear::all();

        // Latest market price
        $latestPrice = MarketPrice::latest('date')->first();

        // Filter by fiscal year if requested
        $transactionsQuery = LatexTransaction::with('plot.farmer');

        if ($request->filled('production_year_id')) {
            $year = ProductionYear::find($request->production_year_id);
            if ($year) {
                $transactionsQuery->whereBetween('transaction_date', [$year->start_date, $year->end_date]);
            }
        }

        $transactions = $transactionsQuery->get();

        // Step 1: Monthly production trends
        $monthlyProduction = $transactions->groupBy(function ($t) {
            return Carbon::parse($t->transaction_date)->format('Y-m');
        })->map(function ($group) {
            return $group->sum(function ($t) {
                return $t->volume_kg * ($t->dry_rubber_content / 100);
            });
        });

        // Step 2: Seasonal patterns (month number)
        $seasonalPattern = $transactions->groupBy(function ($t) {
            return Carbon::parse($t->transaction_date)->format('m');
        })->map(function ($group) {
            return $group->sum(function ($t) {
                return $t->volume_kg * ($t->dry_rubber_content / 100);
            });
        });

        // Step 3: Latex quality evaluation (average DRC per plot)
        $qualityByPlot = $transactions->groupBy('plot_id')->map(function ($group) {
            return [
                'plot_name' => $group->first()->plot->plot_location,
                'farmer_name' => $group->first()->plot->farmer->name,
                'avg_drc' => round($group->avg('dry_rubber_content'), 2)
            ];
        });

        // Step 4: Revenue estimation using latest market price
        $revenueByPlot = $transactions->groupBy('plot_id')->map(function ($group) use ($latestPrice) {
            $plotName = $group->first()->plot->plot_location;
            $farmerName = $group->first()->plot->farmer->name;

            $estimatedRevenue = 0;
            if ($latestPrice) {
                $estimatedRevenue = 0;
                foreach ($group as $t) {
                    $estimatedRevenue += ($t->volume_kg * ($t->dry_rubber_content / 100)) * $latestPrice->price_per_kg;
                }
                $estimatedRevenue = round($estimatedRevenue, 2);
            }

            return [
                'plot_name' => $plotName,
                'farmer_name' => $farmerName,
                'estimated_revenue' => $estimatedRevenue
            ];
        });

        return view('admin.analytics.index', compact(
            'transactions',
            'plots',
            'years',
            'monthlyProduction',
            'seasonalPattern',
            'qualityByPlot',
            'revenueByPlot',
            'latestPrice'
        ));
    }

    /**
     * Forecasting & Recommendations
     */
    public function forecasting()
    {
        $plots = Plot::with('farmer')->get();

        $forecastData = $plots->map(function ($plot) {
            $transactions = LatexTransaction::where('plot_id', $plot->id)
                ->orderBy('transaction_date')
                ->get();

            if ($transactions->isEmpty()) {
                return [
                    'plot_name' => $plot->plot_location,
                    'farmer_name' => $plot->farmer->name,
                    'forecast_dry_rubber' => 0,
                    'forecast_drc' => 0,
                    'forecast_revenue' => 0,
                    'best_month' => 'N/A',
                ];
            }

            // Moving average of last 3 transactions
            $last3 = $transactions->take(-3);

            $forecastDryRubber = $last3->avg(fn($t) => $t->volume_kg * ($t->dry_rubber_content / 100));
            $forecastDRC = $last3->avg('dry_rubber_content');
            $forecastRevenue = $last3->avg('total_amount');

            // Month with highest historical yield
            $bestMonth = $transactions
                ->groupBy(fn($t) => $t->transaction_date->format('F'))
                ->map(fn($group) => $group->sum(fn($t) => $t->volume_kg * ($t->dry_rubber_content / 100)))
                ->sortDesc()
                ->keys()
                ->first();

            return [
                'plot_name' => $plot->plot_location,
                'farmer_name' => $plot->farmer->name,
                'forecast_dry_rubber' => round($forecastDryRubber, 2),
                'forecast_drc' => round($forecastDRC, 2),
                'forecast_revenue' => round($forecastRevenue, 2),
                'best_month' => $bestMonth,
            ];
        });

        return view('admin.analytics.forecasting', compact('forecastData'));
    }
}
