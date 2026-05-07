<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    public function __construct()
    {
        // Only admins can access these routes
        $this->middleware(['auth', 'role:admin']);
    }

    // Show pending users
    public function index()
    {
        $pendingUsers = User::where('is_approved', false)->get();
        return view('admin.pending-users', compact('pendingUsers'));
    }

    // Approve a user
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->save();

        return redirect()->back()->with('success', "$user->name has been approved.");
    }

    // Reject a user (delete or deny)
    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', "$user->name has been rejected.");
    }
}
