<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketPrice;

class MarketPriceController extends Controller
{
    public function index()
    {
        $prices = MarketPrice::latest('date')->paginate(10);
        return view('admin.market_prices.index', compact('prices'));
    }

    public function create()
    {
        return view('admin.market_prices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'price_per_kg' => 'required|numeric|min:0',
            'date' => 'required|date|unique:market_prices,date',
            'source' => 'nullable|string'
        ]);

        MarketPrice::create($request->all());

        return redirect()->route('admin.market-prices.index')->with('success', 'Market price added successfully!');
    }

    public function edit(MarketPrice $marketPrice)
    {
        return view('admin.market_prices.edit', compact('marketPrice'));
    }

    public function update(Request $request, MarketPrice $marketPrice)
    {
        $request->validate([
            'price_per_kg' => 'required|numeric|min:0',
            'date' => 'required|date|unique:market_prices,date,' . $marketPrice->id,
            'source' => 'nullable|string'
        ]);

        $marketPrice->update($request->all());

        return redirect()->route('admin.market-prices.index')->with('success', 'Market price updated successfully!');
    }

    public function destroy(MarketPrice $marketPrice)
    {
        $marketPrice->delete();
        return redirect()->route('admin.market-prices.index')->with('success', 'Market price deleted successfully!');
    }
}
