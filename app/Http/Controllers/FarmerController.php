<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    public function index()
    {
        $farmers = Farmer::all();
        return view('staff.farmer.index', compact('farmers'));
    }

    public function create()
    {
        return view('staff.farmer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:farmers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'farm_location' => 'nullable|string|max:255',
            'farm_size' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        Farmer::create($request->all());

        return redirect()->route('farmer.index')->with('success', 'Farmer added successfully.');
    }

    public function edit(Farmer $farmer)
    {
        return view('staff.farmer.edit', compact('farmer'));
    }

    public function update(Request $request, Farmer $farmer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:farmers,email,' . $farmer->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'farm_location' => 'nullable|string|max:255',
            'farm_size' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $farmer->update($request->all());

        return redirect()->route('farmer.index')->with('success', 'Farmer updated successfully.');
    }

    public function destroy(Farmer $farmer)
    {
        $farmer->delete();
        return redirect()->route('farmer.index')->with('success', 'Farmer deleted successfully.');
    }
}
