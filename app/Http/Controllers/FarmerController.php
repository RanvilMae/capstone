<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    /**
     * Display a listing of farmers with search filtering and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $farmers = Farmer::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('farm_location', 'like', "%{$search}%");

                    // Optional: include code search if present on model
                    if (\Schema::hasColumn('farmers', 'code')) {
                        $q->orWhere('code', 'like', "%{$search}%");
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('farmer.index', compact('farmers'));
    }

    /**
     * Show the form for creating a new farmer.
     */
    public function create()
    {
        return view('farmer.create');
    }

    /**
     * Store a newly created farmer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:farmers,email',
            'phone'         => 'required|string|max:20',
            'address'       => 'nullable|string|max:255',
            'farm_location' => 'nullable|string|max:255',
            'farm_size'     => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        Farmer::create($validated);

        return redirect()
            ->route('main.farmer.index')
            ->with('success', 'Farmer added successfully.');
    }

    /**
     * Show the form for editing the specified farmer.
     */
    public function edit(Farmer $farmer)
    {
        return view('farmer.edit', compact('farmer'));
    }

    /**
     * Update the specified farmer in storage.
     */
    public function update(Request $request, Farmer $farmer)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:farmers,email,' . $farmer->id,
            'phone'         => 'required|string|max:20',
            'address'       => 'nullable|string|max:255',
            'farm_location' => 'nullable|string|max:255',
            'farm_size'     => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $farmer->update($validated);

        return redirect()
            ->route('main.farmer.index')
            ->with('success', 'Farmer updated successfully.');
    }

    /**
     * Remove the specified farmer from storage.
     */
    public function destroy(Farmer $farmer)
    {
        $farmer->delete();

        return redirect()
            ->route('main.farmer.index')
            ->with('success', 'Farmer deleted successfully.');
    }
}