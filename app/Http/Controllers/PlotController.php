<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlotController extends Controller
{
    /**
     * Display a listing of plots with optional search filtering.
     */
    public function index(Request $request)
    {
        $query = Plot::with('farmer');

        // Apply search filter across code, location, notes, and farmer name
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('plot_location', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('farmer', function ($farmerQuery) use ($search) {
                      $farmerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Paginate and retain the search query parameter in pagination links
        $plots = $query->paginate(10)->withQueryString();

        return view('plots.index', compact('plots'));
    }

    /**
     * Show the form for creating a new plot.
     */
    public function create()
    {
        $farmers = Farmer::all();
        return view('plots.create', compact('farmers'));
    }

    /**
     * Store a newly created plot in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'farmer_id'     => 'required|exists:farmers,id',
            'plot_size_rai' => 'required|numeric|min:0.01',
            'plot_location' => 'required|string|max:255', // Unique restriction removed
            'notes'         => 'nullable|string',
        ]);

        Plot::create($request->all());

        return redirect()->route('plots.index')->with('success', 'Plot created successfully.');
    }

    public function update(Request $request, Plot $plot)
    {
        $request->validate([
            'farmer_id'     => 'required|exists:farmers,id',
            'plot_size_rai' => 'required|numeric|min:0.01',
            'plot_location' => 'required|string|max:255', // Unique restriction removed
            'notes'         => 'nullable|string',
        ]);

        $plot->update($request->all());

        return redirect()->route('plots.index')->with('success', 'Plot updated successfully.');
    }

    /**
     * Show the form for editing the specified plot.
     */
    public function edit(Plot $plot)
    {
        $farmers = Farmer::all();
        return view('plots.edit', compact('plot', 'farmers'));
    }



    /**
     * Remove the specified plot from storage.
     */
    public function destroy(Plot $plot)
    {
        $plot->delete();

        return redirect()->route('plots.index')->with('success', 'Plot deleted successfully.');
    }
}