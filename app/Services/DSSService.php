<?php

namespace App\Services;

class DSSService
{
    public function getRecommendation($rainfall, $temp)
    {
        // Decision Logic based on Krabi Rubber Learning Center standards
        $score = 10;

        if ($rainfall > 2) $score -= 4; // Light rain lowers DRC
        if ($rainfall > 10) $score -= 6; // Heavy rain causes washout
        if ($temp > 32) $score -= 2;    // Heat causes latex to coagulate early
        
        if ($score >= 7) return ['action' => 'TAP', 'color' => 'green', 'score' => $score];
        if ($score >= 4) return ['action' => 'CAUTION', 'color' => 'yellow', 'score' => $score];
        return ['action' => 'SKIP', 'color' => 'red', 'score' => $score];
    }
}