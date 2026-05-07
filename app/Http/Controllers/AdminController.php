<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    // ==============================
    // USER MANAGEMENT
    // ==============================

    public function createUser()
    {
        return view('admin.users.create-user');
    }

    public function manageUsers(Request $request)
    {
        $users = User::all();

        if ($request->expectsJson()) {
            return response()->json($users, Response::HTTP_OK);
        }

        return view('admin.users.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,staff,director,farmer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], Response::HTTP_CREATED);
        }

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }



    // ==============================
    // PENDING USERS
    // ==============================

    public function pendingUsers(Request $request)
    {
        // Show users who are pending (not approved and not rejected)
        $users = User::where('is_approved', false)
            ->where('is_rejected', false)
            ->get();

        if ($request->expectsJson()) {
            return response()->json($users, Response::HTTP_OK);
        }

        return view('admin.pending-users', compact('users'));
    }

    public function approve(Request $request, User $user)
    {
        $user->update([
            'is_approved' => true,
            'approved' => true,
        ]);

        return back()->with('success', "$user->name has been approved.");
    }

    public function reject(Request $request, User $user)
    {
        $user->update([
            'is_approved' => false,
            'is_rejected' => true,
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => 'User rejected'], Response::HTTP_OK)
            : back()->with('success', "$user->name has been rejected.");
    }

    public function destroyUser(User $user)
    {
        $user->delete(); // soft delete

        return redirect()->back()->with('success', "$user->name has been deleted.");
    }

    // ==============================
// RESTORE USER (optional)
// ==============================
    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()->with('success', "$user->name has been restored.");
    }
    // ==============================
    // FARMER MANAGEMENT
    // ==============================

    public function listFarmers(Request $request)
    {
        $farmers = Farmer::all();

        if ($request->expectsJson()) {
            return response()->json($farmers, Response::HTTP_OK);
        }

        return view('admin.farmer.index', compact('farmers'));
    }

    public function createFarmer()
    {
        return view('admin.farmer.create');
    }

    public function editFarmer(Farmer $farmer)
    {
        return view('admin.farmer.edit', compact('farmer'));
    }

    public function storeFarmer(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:farmers,email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $farmer = Farmer::create(array_merge($data, [
            'farm_location' => $request->farm_location,
            'farm_size' => $request->farm_size,
            'notes' => $request->notes,
        ]));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Farmer created successfully',
                'data' => $farmer
            ], Response::HTTP_CREATED);
        }

        return redirect()->route('admin.farmer.index')
            ->with('success', 'Farmer added successfully.');
    }

    public function updateFarmer(Request $request, Farmer $farmer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:farmers,email,' . $farmer->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $farmer->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Farmer updated successfully',
                'data' => $farmer
            ], Response::HTTP_OK);
        }

        return redirect()->route('admin.farmer.index')
            ->with('success', 'Farmer updated successfully.');
    }

    public function destroyFarmer(Request $request, Farmer $farmer)
    {
        $farmer->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Farmer deleted successfully'
            ], Response::HTTP_OK);
        }

        return redirect()->route('admin.farmer.index')
            ->with('success', 'Farmer deleted successfully.');
    }
}
