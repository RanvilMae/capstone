<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    /**
     * Display a listing of farmers (for both Admin and Staff).
     */
    public function index()
    {
        $farmers = Farmer::all();
        return view('farmer.index', compact('farmers')); // shared view
    }

    /**
     * Show the form for creating a new farmer.
     */
    public function create()
    {
        return view('farmer.create'); // shared view
    }

    /**
     * Store a newly created farmer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:farmers,email',
            'phone'         => 'required|string|max:20',
            'address'       => 'nullable|string|max:255',
            'farm_location' => 'nullable|string|max:255',
            'farm_size'     => 'nullable|numeric',
            'notes'         => 'nullable|string',
        ]);

        Farmer::create($request->all());

        return redirect()->route('farmer.index')->with('success', 'Farmer added successfully.');
    }

    /**
     * Show the form for editing the specified farmer.
     */
    public function edit(Farmer $farmer)
    {
        return view('farmer.edit', compact('farmer')); // shared view
    }

    /**
     * Update the specified farmer in storage.
     */
    public function update(Request $request, Farmer $farmer)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:farmers,email,' . $farmer->id,
            'phone'         => 'required|string|max:20',
            'address'       => 'nullable|string|max:255',
            'farm_location' => 'nullable|string|max:255',
            'farm_size'     => 'nullable|numeric',
            'notes'         => 'nullable|string',
        ]);

        $farmer->update($request->all());

        return redirect()->route('farmer.index')->with('success', 'Farmer updated successfully.');
    }

    /**
     * Remove the specified farmer from storage.
     */
    public function destroy(Farmer $farmer)
    {
        $farmer->delete();

        return redirect()->route('farmer.index')->with('success', 'Farmer deleted successfully.');
    }
}
