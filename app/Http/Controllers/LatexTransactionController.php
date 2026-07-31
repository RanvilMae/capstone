<?php
// app/Http/Controllers/LatexTransactionController.php
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
        $query = LatexTransaction::query()
            ->select([
                'id', 'plot_id', 'user_id', 'transaction_date', 'location', 
                'volume_kg', 'dry_rubber_content', 'dry_rubber_weight_kg', 
                'price_per_kg', 'total_amount', 'quality_classification'
            ])
            ->with([
                'plot:id,plot_location,farmer_id',
                'plot.farmer:id,name',
                'user:id,name'
            ]);

        // Apply Filter Parameters
        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }

        if ($request->filled('farmer_id')) {
            $query->whereHas('plot', function ($q) use ($request) {
                $q->where('farmer_id', $request->farmer_id);
            });
        }

        // Paginate transactions (25 records per page)
        $transactions = $query->latest('transaction_date')->paginate(25);

        // Calculate aggregated totals per plot directly in SQL database
        $totals = DB::table('latex_transactions')
            ->join('plots', 'latex_transactions.plot_id', '=', 'plots.id')
            ->join('farmers', 'plots.farmer_id', '=', 'farmers.id')
            ->select(
                'plots.plot_location',
                'plots.code as plot_code', // <--- Make sure this line is present
                'farmers.name as farmer_name',
                DB::raw('SUM(dry_rubber_weight_kg) as total_dry_rubber'),
                DB::raw('SUM(total_amount) as total_income')
            )
            ->groupBy('plots.id', 'plots.plot_location', 'plots.code', 'farmers.name')
            ->get();

        // Load filter options efficiently
        $plots = Plot::select('id', 'plot_location', 'plot_size_rai', 'farmer_id')
            ->with('farmer:id,name')
            ->get();

        $farmers = Farmer::select('id', 'name')->get();

        return view('transactions.index', compact('transactions', 'totals', 'plots', 'farmers'));
    }

    public function create()
    {
        $plots = Plot::with('farmer')->get();

        // Single view for Admin and Staff
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

        // Average dry weight
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

        // Redirect to transactions page for both Admin and Staff
        return redirect()->route('transactions.create')->with('success', 'Transaction saved successfully.');
    }

    private function updateProductionSummary(LatexTransaction $transaction)
    {
        $plot = $transaction->plot;
        $year = ProductionYear::where('start_date', '<=', $transaction->transaction_date)
            ->where('end_date', '>=', $transaction->transaction_date)
            ->first();

        if (!$year) return;

        $summary = ProductionSummary::firstOrCreate(
            [
                'plot_id' => $plot->id,
                'production_year_id' => $year->id
            ],
            [
                'dry_rubber_weight_kg' => 0,
                'total_amount_baht' => 0
            ]
        );

        // Recalculate totals
        $transactions = LatexTransaction::where('plot_id', $plot->id)
            ->whereBetween('transaction_date', [$year->start_date, $year->end_date])
            ->get();

        $summary->dry_rubber_weight_kg = $transactions->sum(fn($t) => $t->volume_kg * ($t->dry_rubber_content / 100));
        $summary->total_amount_baht = $transactions->sum('total_amount');
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
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new LatexProductionImport, $request->file('excel_file'));

            return redirect()->back()->with('success', 'Data Integrated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to import data: ' . $e->getMessage());
        }
    }

    public function showImportForm()
    {
        // Renders resources/views/latex/import.blade.php
        return view('latex.import');
    }
}
