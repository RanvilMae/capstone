<?php

namespace App\Services;

use App\Models\Plot;
use Carbon\Carbon;

class YieldForecastService
{
    public function calculateForecast(
        int $plotId,
        string $targetHarvestDate,
        int $scheduledTappingDays,
        float $targetRevenueGoal,
        float $estimatedDrc,
        float $targetPricePerKg
    ): array {
        $plot = Plot::with('latexTransactions')->findOrFail($plotId);

        // 1. Plot Size Handling
        $plotSizeRai = (float) ($plot->plot_size_rai > 0 ? $plot->plot_size_rai : 1.0);

        // 2. Compute Yield Density Dynamic (Fallback to 15.0 if no transaction history)
        $totalDryWeight = $plot->latexTransactions->sum('dry_rubber_weight_kg');
        $totalHarvests = $plot->latexTransactions->count();

        if ($totalHarvests > 0 && $plotSizeRai > 0) {
            // Average dry rubber yield per tapping session per Rai
            $yieldDensityPerRai = ($totalDryWeight / $totalHarvests) / $plotSizeRai;
        } else {
            // Fallback default density per Rai (kg/Rai per day)
            $yieldDensityPerRai = 15.0;
        }

        // 3. Seasonal Adjustment Multiplier
        $month = Carbon::parse($targetHarvestDate)->month;
        $seasonalMultiplier = match (true) {
            in_array($month, [10, 11, 12, 1]) => 1.25, // High-yield peak season
            in_array($month, [2, 3, 4])       => 0.75, // Low-yield defoliation / wintering season
            default                           => 1.00,
        };

        // 4. Forecast Math Calculations
        // Estimated daily raw latex (kg) considering DRC conversion
        $drcFactor = $estimatedDrc > 0 ? ($estimatedDrc / 100) : 0.32;
        $dailyDryKg = $yieldDensityPerRai * $plotSizeRai * $seasonalMultiplier;
        $dailyRawKg = $dailyDryKg / $drcFactor;

        $projectedRawLatexKg = round($dailyRawKg * $scheduledTappingDays, 2);
        $projectedDryRubberKg = round($projectedRawLatexKg * $drcFactor, 2);
        $projectedRevenue = round($projectedDryRubberKg * $targetPricePerKg, 2);

        // 5. Target Completion Rate
        $completionRate = $targetRevenueGoal > 0 
            ? round(($projectedRevenue / $targetRevenueGoal) * 100, 2) 
            : 100.0;

        // 6. DSS Advisory Generation
        if ($completionRate >= 100) {
            $status = 'optimal';
            $message = "Goal achievable! Projected income (฿" . number_format($projectedRevenue, 2) . ") exceeds target goal of ฿" . number_format($targetRevenueGoal, 2) . " across {$scheduledTappingDays} tapping days.";
        } else {
            $status = 'warning';
            $message = "Target goal is ambitious for this plot. Projected revenue reaches {$completionRate}% of your ฿" . number_format($targetRevenueGoal, 2) . " target.";
        }

        return [
            'projected_raw_latex_kg'  => $projectedRawLatexKg,
            'projected_dry_rubber_kg' => $projectedDryRubberKg,
            'projected_revenue'       => $projectedRevenue,
            'completion_rate'         => $completionRate,
            'advisory_status'         => $status,
            'advisory_message'        => $message,
        ];
    }
}