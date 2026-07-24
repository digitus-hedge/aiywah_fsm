<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsReset
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('worker')->user();

        if ($user && $user->must_reset_password) {
            if (!$request->routeIs('worker.password.forced', 'worker.password.forced.update', 'worker.logout')) {
                return redirect()->route('worker.password.forced');
            }
        }

        return $next($request);
    }
}