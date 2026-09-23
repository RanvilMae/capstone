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
        $plot = Plot::findOrFail($plotId);

        // 1. Plot & Yield calculations
        $plotSizeRai = $plot->plot_size_rai ?? 1;
        $yieldDensityPerRai = $plot->yield_density_kg_per_rai ?? 15.0;

        // 2. Seasonal Multiplier
        $month = Carbon::parse($targetHarvestDate)->month;
        $seasonalMultiplier = match (true) {
            in_array($month, [10, 11, 12, 1]) => 1.25, // High yield season
            in_array($month, [2, 3, 4])       => 0.75, // Low yield / defoliation season
            default                          => 1.00,
        };

        // 3. Projections
        $dailyRawKg = $yieldDensityPerRai * $plotSizeRai * $seasonalMultiplier;
        $projectedRawLatexKg = round($dailyRawKg * $scheduledTappingDays, 2);
        
        $projectedDryRubberKg = round($projectedRawLatexKg * ($estimatedDrc / 100), 2);
        $projectedRevenue = round($projectedDryRubberKg * $targetPricePerKg, 2);

        // 4. Feasibility Completion Rate
        $completionRate = $targetRevenueGoal > 0 
            ? round(($projectedRevenue / $targetRevenueGoal) * 100, 2) 
            : 100;

        // 5. DSS Advisory
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