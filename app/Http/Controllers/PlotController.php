<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlotController extends Controller
{
    public function index()
    {
        // Fetch plots with farmers, paginate for all users
        $plots = Plot::with('farmer')->paginate(10);
        return view('plots.index', compact('plots')); // Updated view path
    }

    public function create()
    {
        $farmers = Farmer::all();
        return view('plots.create', compact('farmers')); // Updated view path
    }

    public function store(Request $request)
    {
        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'plot_size_rai' => 'required|numeric',
            'plot_location' => [
                'required',
                'string',
                'max:255',
                // Ensure uniqueness per farmer
                Rule::unique('plots')->where(fn ($query) => $query->where('farmer_id', $request->farmer_id)),
            ],
            'notes' => 'nullable|string',
        ], [
            'plot_location.unique' => 'This farmer already has a plot at this location.',
        ]);

        Plot::create($request->all());

        // Redirect back to create page with success message
        return redirect()->route('plots.create')->with('success', 'Plot created successfully.');
    }

    public function edit(Plot $plot)
    {
        $farmers = Farmer::all();
        return view('plots.edit', compact('plot', 'farmers')); // Updated view path
    }

    public function update(Request $request, Plot $plot)
    {
        $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'plot_size_rai' => 'required|numeric',
            'plot_location' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plots')->where(fn ($query) => $query->where('farmer_id', $request->farmer_id))
                                        ->ignore($plot->id),
            ],
            'notes' => 'nullable|string',
        ], [
            'plot_location.unique' => 'This farmer already has a plot at this location.',
        ]);

        $plot->update($request->all());

        // Redirect back to edit page with success message
        return redirect()->route('plots.edit', $plot->id)->with('success', 'Plot updated successfully.');
    }

    public function destroy(Plot $plot)
    {
        $plot->delete();

        // Redirect back to index with success message
        return redirect()->route('plots.index')->with('success', 'Plot deleted successfully.');
    }
}
