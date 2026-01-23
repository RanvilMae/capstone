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

        // Filters
        $query = LatexTransaction::with('plot.farmer', 'user');

        if ($request->filled('plot_id')) {
            $query->where('plot_id', $request->plot_id);
        }

        if ($request->filled('farmer_id')) {
            $query->whereHas('plot', function ($q) use ($request) {
                $q->where('farmer_id', $request->farmer_id);
            });
        }

        if ($request->filled('production_year_id')) {
            $year = ProductionYear::find($request->production_year_id);
            if ($year) {
                $query->whereBetween('transaction_date', [$year->start_date, $year->end_date]);
            }
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);

        // Calculate totals per plot
        $totals = $transactions->groupBy('plot_id')->map(function ($group) {
            return [
                'dry_rubber_weight' => $group->sum(function ($t) {
                    return $t->volume_kg * ($t->dry_rubber_content / 100); }),
                'total_income' => $group->sum('total_amount')
            ];
        });

        return view('transactions.index', compact('transactions', 'plots', 'years', 'totals'));
    }

    public function create()
    {
        // Admin sees all plots
        $plots = Plot::with('farmer')->get();
        return view('transactions.create', compact('plots'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'transaction_date' => 'required|date',
            'volume_kg' => 'required|numeric|min:0',
            'dry_rubber_content' => 'required|numeric|min:0|max:100',
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        // Calculate total_amount
        $data['total_amount'] = ($data['volume_kg'] * ($data['dry_rubber_content'] / 100)) * $data['price_per_kg'];
        $data['user_id'] = Auth::id();

        // Save transaction
        $transaction = LatexTransaction::create($data);

        // Update Production Summary automatically
        $this->updateProductionSummary($transaction);

        return redirect()->back()->with('success', 'Transaction recorded and summary updated!');
    }

    private function updateProductionSummary(LatexTransaction $transaction)
    {
        $plot = $transaction->plot;
        $year = ProductionYear::where('start_date', '<=', $transaction->transaction_date)
            ->where('end_date', '>=', $transaction->transaction_date)
            ->first();

        if (!$year)
            return; // Safety check

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

        // Recalculate totals from all transactions for this plot/year
        $transactions = LatexTransaction::where('plot_id', $plot->id)
            ->whereBetween('transaction_date', [$year->start_date, $year->end_date])
            ->get();

        $summary->dry_rubber_weight_kg = $transactions->sum(function ($t) {
            return $t->volume_kg * ($t->dry_rubber_content / 100);
        });

        $summary->total_amount_baht = $transactions->sum('total_amount');
        $summary->save();
    }
}
