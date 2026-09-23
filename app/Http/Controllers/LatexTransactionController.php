<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LatexTransaction;
use App\Models\Plot;
use App\Models\Farmer;
use App\Models\ProductionSummary;
use App\Models\ProductionYear;
use Illuminate\Support\Facades\Auth;
use App\Imports\LatexProductionImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class LatexTransactionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Build query for main transactions list
        $query = LatexTransaction::query()
            ->select([
                'id', 'plot_id', 'user_id', 'transaction_date', 'location', 
                'volume_kg', 'dry_rubber_content', 'dry_rubber_weight_kg', 
                'price_per_kg', 'total_amount', 'quality_classification'
            ])
            ->with([
                'plot:id,plot_location,code,farmer_id',
                'plot.farmer:id,name',
                'user:id,name'
            ]);

        // Filter transactions by transaction_date year
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        // Filter transactions by Plot
        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }

        // Filter transactions by Farmer
        if ($request->filled('farmer_id')) {
            $query->whereHas('plot', function ($q) use ($request) {
                $q->where('farmer_id', $request->farmer_id);
            });
        }

        // Paginate transactions list (uses default 'page' query parameter)
        $transactions = $query->latest('transaction_date')
            ->paginate(25)
            ->withQueryString();

        // 2. Build aggregated totals query per plot with custom pagination
        $totalsQuery = DB::table('latex_transactions')
            ->leftJoin('plots', 'latex_transactions.plot_id', '=', 'plots.id')
            ->leftJoin('farmers', 'plots.farmer_id', '=', 'farmers.id')
            ->select(
                'plots.id as plot_id',
                'plots.plot_location',
                'plots.code as plot_code',
                'plots.plot_size_rai',
                'farmers.name as farmer_name',
                DB::raw('COALESCE(SUM(latex_transactions.dry_rubber_weight_kg), 0) as total_dry_rubber'),
                DB::raw('COALESCE(SUM(latex_transactions.total_amount), 0) as total_income')
            );

        // Filter totals by transaction_date year
        if ($request->filled('year')) {
            $totalsQuery->whereYear('latex_transactions.transaction_date', $request->year);
        }

        // Filter totals by Plot
        if ($request->filled('plot_id')) {
            $totalsQuery->where('latex_transactions.plot_id', $request->plot_id);
        }

        // Filter totals by Farmer
        if ($request->filled('farmer_id')) {
            $totalsQuery->where('plots.farmer_id', $request->farmer_id);
        }

        // Grouping and Paginating 10 items per page with custom page parameter 'totals_page'
        $totals = $totalsQuery
            ->groupBy('plots.id', 'plots.plot_location', 'plots.code', 'plots.plot_size_rai', 'farmers.name')
            ->paginate(10, ['*'], 'totals_page')
            ->withQueryString();

        // 3. Extract distinct years from transaction_date
        $years = LatexTransaction::whereNotNull('transaction_date')
            ->selectRaw('YEAR(transaction_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // 4. Load options for remaining filter dropdowns
        $plots = Plot::select('id', 'code', 'plot_location', 'plot_size_rai', 'farmer_id')
            ->with('farmer:id,name')
            ->get();

        $farmers = Farmer::select('id', 'name')->get();

        return view('transactions.index', compact('transactions', 'totals', 'plots', 'farmers', 'years'));
    }

    public function create()
    {
        $plots = Plot::with('farmer')->get();

        return view('transactions.create', compact('plots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'transaction_date' => 'required|date',
            'volume_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'location' => 'nullable|string',
            'drc_sample_1' => 'nullable|numeric',
            'drc_sample_2' => 'nullable|numeric',
            'drc_sample_3' => 'nullable|numeric',
            'dry_sample_1' => 'nullable|numeric',
            'dry_sample_2' => 'nullable|numeric',
            'dry_sample_3' => 'nullable|numeric',
        ]);

        $freshWeight = $request->volume_kg;

        // Average DRC calculation
        $drcSamples = array_filter([$request->drc_sample_1, $request->drc_sample_2, $request->drc_sample_3], fn($v) => $v !== null);
        $avgDRC = count($drcSamples) ? array_sum($drcSamples) / count($drcSamples) : 0;

        // Average dry weight calculation
        $drySamples = array_filter([$request->dry_sample_1, $request->dry_sample_2, $request->dry_sample_3], fn($v) => $v !== null);
        $avgDryWeight = count($drySamples) ? array_sum($drySamples) / count($drySamples) : ($freshWeight * ($avgDRC / 100));

        // Save transaction
        $transaction = LatexTransaction::create([
            'plot_id' => $request->plot_id,
            'location' => $request->location,
            'transaction_date' => $request->transaction_date,
            'volume_kg' => $freshWeight,
            'dry_rubber_content' => $avgDRC,
            'drc_sample_1' => $request->drc_sample_1,
            'drc_sample_2' => $request->drc_sample_2,
            'drc_sample_3' => $request->drc_sample_3,
            'dry_sample_1' => $request->dry_sample_1,
            'dry_sample_2' => $request->dry_sample_2,
            'dry_sample_3' => $request->dry_sample_3,
            'dry_rubber_weight_kg' => $avgDryWeight,
            'price_per_kg' => $request->price_per_kg,
            'total_amount' => $avgDryWeight * $request->price_per_kg,
            'user_id' => Auth::id(),
        ]);

        $this->updateProductionSummary($transaction);

        return redirect()->route('transactions.create')->with('success', 'Transaction saved successfully.');
    }

    private function updateProductionSummary(LatexTransaction $transaction)
    {
        $year = ProductionYear::where('start_date', '<=', $transaction->transaction_date)
            ->where('end_date', '>=', $transaction->transaction_date)
            ->first();

        if (!$year) return;

        $summary = ProductionSummary::firstOrCreate(
            [
                'plot_id' => $transaction->plot_id,
                'production_year_id' => $year->id
            ],
            [
                'dry_rubber_weight_kg' => 0,
                'total_amount_baht' => 0
            ]
        );

        // Recalculate totals
        $totals = LatexTransaction::where('plot_id', $transaction->plot_id)
            ->whereBetween('transaction_date', [$year->start_date, $year->end_date])
            ->selectRaw('SUM(dry_rubber_weight_kg) as total_weight, SUM(total_amount) as total_amount')
            ->first();

        $summary->dry_rubber_weight_kg = $totals->total_weight ?? 0;
        $summary->total_amount_baht = $totals->total_amount ?? 0;
        $summary->save();
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new LatexProductionImport, $request->file('excel_file'));

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Batch spreadsheet ingested and persisted successfully.',
                ], 200);
            }

            return redirect()->back()->with('success', 'Batch spreadsheet ingested and persisted successfully.');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Import Error: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', 'Import Error: ' . $e->getMessage());
        }
    }

    public function uploadExcel(Request $request)
    {
        return $this->import($request);
    }

    public function showImportForm()
    {
        return view('latex.import');
    }
}