<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Approved
{
    public function handle(Request $request, Closure $next)
    {
        // Must be logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Optional: allow admin to bypass approval
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check approval status
        if (!$user->is_approved) {
            return redirect()
                ->route('login')
                ->with('error', 'Your account is pending admin approval.');
        }

        return $next($request);
    }
}
