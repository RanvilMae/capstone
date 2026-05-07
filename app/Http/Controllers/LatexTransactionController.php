<?php
// app/Http/Controllers/LatexTransactionController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LatexTransaction;
use App\Models\Plot;
use App\Models\ProductionSummary;
use App\Models\ProductionYear;
use Illuminate\Support\Facades\Auth;

class LatexTransactionController extends Controller
{
    public function index(Request $request)
    {
        $plots = Plot::with('farmer')->get();
        $years = ProductionYear::all();

        $query = LatexTransaction::with('plot.farmer', 'user');

        // Filters
        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }

        if ($request->filled('farmer_id')) {
            $query->whereHas('plot', fn($q) => $q->where('farmer_id', $request->farmer_id));
        }

        if ($request->filled('production_year_id')) {
            $year = ProductionYear::find($request->production_year_id);
            if ($year) {
                $query->whereBetween('transaction_date', [$year->start_date, $year->end_date]);
            }
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);

        // Calculate totals per plot
        $totals = $transactions->groupBy('plot_id')->map(fn($group) => [
            'dry_rubber_weight' => $group->sum(fn($t) => $t->volume_kg * ($t->dry_rubber_content / 100)),
            'total_income' => $group->sum('total_amount')
        ]);

        return view('transactions.index', compact('transactions', 'plots', 'years', 'totals'));
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
}
