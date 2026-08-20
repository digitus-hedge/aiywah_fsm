<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureWorkerIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('worker')->user();

        if ($user && $user->status === 'inactive') {
            Auth::guard('worker')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('worker.login')
                ->with('status', 'Your account has been deactivated. Please contact your supervisor.');
        }

        return $next($request);
    }
}