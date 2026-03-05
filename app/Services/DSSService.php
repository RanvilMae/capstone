<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DSSService
{
    /**
     * Real-time Daily Recommendation
     */
    public function getRecommendation($rain = 0, $temp = 0, $humidity = 0, $wind = 0)
    {
        // Base score starts at 10
        $score = 10;

        // 1. Rainfall Impact (Primary)
        if ($rain > 5) $score -= 7; // Washout risk
        elseif ($rain > 0) $score -= 3; // Interference risk

        // 2. Humidity Impact (Latex Coagulation)
        // High humidity (>85%) prevents the latex from drying, leading to "late drip" or fungal issues
        if ($humidity > 85) $score -= 1;

        // 3. Wind Speed Impact (Physical Risk)
        // Winds > 8 m/s are dangerous for high-panel tapping
        if ($wind > 8) $score -= 2;

        // Determine Status
        if ($score >= 7) {
            return ['score' => $score, 'recommendation' => 'Optimal for Tapping', 'color' => 'green'];
        } elseif ($score >= 4) {
            return ['score' => $score, 'recommendation' => 'Proceed with Caution', 'color' => 'yellow'];
        } else {
            return ['score' => $score, 'recommendation' => 'High Risk / No Tapping', 'color' => 'red'];
        }
    }
    /**
     * LEGIT LOGIC: Monthly Research Recommendation
     * Analyzes yield density vs baseline to give a retrospective score
     */
    public function getMonthlyRecommendation($avgYield, $avgDRC)
    {
        // Baseline: A healthy Krabi plot typically yields ~450-500kg dry weight monthly
        // Baseline: Standard DRC (Dry Rubber Content) should be > 30%
        $score = 0;

        // Yield Score (0-6 points)
        if ($avgYield > 450) $score += 6;
        elseif ($avgYield > 300) $score += 4;
        elseif ($avgYield > 150) $score += 2;

        // Quality/DRC Score (0-4 points)
        if ($avgDRC > 35) $score += 4;
        elseif ($avgDRC > 28) $score += 2;

        return [
            'score' => $score,
            'recommendation' => match (true) {
                $score >= 8 => 'Peak Production Month',
                $score >= 5 => 'Average Yield Cycle',
                $score >= 3 => 'Low Season / Wintering',
                default => 'Critical Production Drop'
            }
        ];
    }

    /**
     * Historical Weather Fetcher
     */
    public function getHistoricalRainfall($date)
    {
        $apiKey = config('services.openweather.key');
        if (!$apiKey) return 0;

        $unixTime = Carbon::parse($date)->timestamp;
        $url = "https://api.openweathermap.org/data/3.0/onecall/timemachine?lat=8.06&lon=98.91&dt={$unixTime}&appid={$apiKey}";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                return $data['data'][0]['rain']['1h'] ?? $data['data'][0]['rain'] ?? 0; 
            }
        } catch (\Exception $e) {
            \Log::error("Historical Weather API Error: " . $e->getMessage());
        }
        return 0;
    }

    
}