<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\Plot;
use App\Models\LatexTransaction;

class AnalyticsController extends Controller
{
    public function index()
    {
        $day = now()->format('l');
        $date = now()->format('F d, Y');
        $temperature = 32; // Replace with real weather API later
        $condition = 'Sunny';
        $icon = '☀️';

        // KPIs
        $totalFarmers = Farmer::count();
        $totalPlots = Plot::count();
        $totalWeight = LatexTransaction::sum('volume_kg');

        // Top contribution per plot
       $topPlot = Plot::with('latexTransactions')
        ->get()
        ->map(function($plot){
            $totalPlotVolume = $plot->latexTransactions->sum('volume_kg');
            $totalVolume = LatexTransaction::sum('volume_kg') ?: 1; // prevent division by zero
            $plot->contribution_percent = round(($totalPlotVolume / $totalVolume) * 100, 2);
            return $plot;
        })
        ->sortByDesc('contribution_percent')
        ->first();



        // Monthly DSS recommendations (dummy example, can be computed)
        $monthlyDSS = [];
        $months = collect(range(1,12));
        foreach($months as $month) {
            $transactions = LatexTransaction::whereMonth('transaction_date', $month)->get();
            $avgDRC = $transactions->avg('dry_rubber_content') ?? 0;

            $monthlyDSS[] = [
                'month' => \Carbon\Carbon::create()->month($month)->format('F'),
                'score' => round($avgDRC, 2),
                'recommendation' => $avgDRC < 30 ? 'Increase tapping quality' : 'Maintain current practices'
            ];
        }

        // Chart data - monthly production trend
        $chartLabels = $months->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('M'));
        $chartData = $months->map(fn($m) => LatexTransaction::whereMonth('transaction_date', $m)->sum('volume_kg'));

        // Quality Index - average dry rubber content across all transactions
        $qualityIndex = round(LatexTransaction::avg('dry_rubber_content') ?? 0);

        return view('admin.analytics.index', compact(
            'day','date','temperature','condition','icon',
            'totalFarmers','totalWeight','totalPlots','topPlot','monthlyDSS',
            'chartLabels','chartData','qualityIndex'
        ));
    }
}
