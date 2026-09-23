<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\LatexTransaction;
use App\Services\YieldForecastService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DecisionSupportApiController extends Controller
{
    protected YieldForecastService $forecastService;

    public function __construct(YieldForecastService $forecastService)
    {
        $this->forecastService = $forecastService;
    }

    public function getYieldPerPlot($plotId): JsonResponse
    {
        $plot = Plot::with(['farmer', 'latexTransactions'])->find($plotId);

        if (!$plot) {
            return response()->json([
                'success' => false,
                'message' => 'Plot not found'
            ], 404);
        }

        $transactions = $plot->latexTransactions;

        $totalDryWeight = $transactions->sum('dry_rubber_weight_kg');
        $totalVolumeRaw = $transactions->sum('volume_kg');
        $avgDrc = $transactions->avg('dry_rubber_content') ?? 0;
        $transactionCount = $transactions->count();

        $yieldPerRai = $plot->plot_size_rai > 0 ? ($totalDryWeight / $plot->plot_size_rai) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'plot_id' => $plot->id,
                'plot_code' => $plot->code,
                'plot_location' => $plot->plot_location,
                'plot_size_rai' => $plot->plot_size_rai,
                'farmer_name' => $plot->farmer ? $plot->farmer->name : 'N/A',
                'total_harvest_events' => $transactionCount,
                'total_raw_volume_kg' => round($totalVolumeRaw, 2),
                'total_dry_rubber_kg' => round($totalDryWeight, 2),
                'average_drc_percent' => round($avgDrc, 2),
                'yield_density_kg_per_rai' => round($yieldPerRai, 2),
                'recent_transactions' => $transactions->sortByDesc('transaction_date')->take(5)->values()
            ]
        ]);
    }

    /**
     * Endpoint 2: Market Sales Prices
     */
    public function getSalesPrices(): JsonResponse
    {
        $latestPrice = LatexTransaction::latest('transaction_date')->value('price_per_kg') ?? 0.00;
        $avgPrice = LatexTransaction::avg('price_per_kg') ?? 0.00;

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'latest_market_price_per_kg' => round($latestPrice, 2),
                'historical_avg_price_per_kg' => round($avgPrice, 2),
                'currency' => 'THB'
            ]
        ]);
    }

    /**
     * Endpoint 3: Calculate Revenue Projection & DSS Advisory
     */
    public function calculateRevenueProjection(Request $request): JsonResponse
    {
        $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'estimated_volume_kg' => 'required|numeric|min:0',
            'estimated_drc' => 'required|numeric|min:0|max:100',
            'target_price_per_kg' => 'nullable|numeric|min:0',
        ]);

        $plot = Plot::findOrFail($request->plot_id);
        $dryRubberWeightKg = $request->estimated_volume_kg * ($request->estimated_drc / 100);

        $pricePerKg = $request->target_price_per_kg 
            ?: (LatexTransaction::latest('transaction_date')->value('price_per_kg') ?? 0.00);

        $projectedRevenue = $dryRubberWeightKg * $pricePerKg;

        if ($request->estimated_drc < 25) {
            $advisory = 'Low DRC Warning: Your Dry Rubber Content is below 25%. Processing or adding coagulation treatment is recommended before selling to improve market grade.';
            $status = 'warning';
        } elseif ($pricePerKg > 0 && $request->estimated_drc >= 35) {
            $advisory = 'Optimal Market Condition: High DRC grade achieved (>35%). Proceed with immediate sale to maximize revenue per Rai.';
            $status = 'optimal';
        } else {
            $advisory = 'Moderate Yield Quality: Standard market conditions apply for this transaction.';
            $status = 'standard';
        }

        return response()->json([
            'success' => true,
            'plot_code' => $plot->code,
            'calculation' => [
                'estimated_volume_kg' => (float)$request->estimated_volume_kg,
                'estimated_drc_percent' => (float)$request->estimated_drc,
                'estimated_dry_rubber_kg' => round($dryRubberWeightKg, 2),
                'price_per_kg_used' => (float)$pricePerKg,
                'projected_total_amount' => round($projectedRevenue, 2),
                'currency' => 'THB'
            ],
            'dss_advisory' => [
                'status' => $status,
                'message' => $advisory
            ]
        ]);
    }

    public function forecastProduction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'target_harvest_date' => 'required|date',
            'scheduled_tapping_days' => 'required|integer|min:1|max:31',
            'target_revenue_goal' => 'required|numeric|min:0',
            'estimated_drc' => 'required|numeric|min:10|max:50',
            'target_price_per_kg' => 'required|numeric|min:1',
        ]);

        $forecast = $this->forecastService->calculateForecast(
            (int) $validated['plot_id'],
            $validated['target_harvest_date'],
            (int) $validated['scheduled_tapping_days'],
            (float) $validated['target_revenue_goal'],
            (float) $validated['estimated_drc'],
            (float) $validated['target_price_per_kg']
        );

        return response()->json([
            'success' => true,
            'forecast' => $forecast
        ]);
    }
}