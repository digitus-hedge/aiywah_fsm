<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureWorker
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('worker')->check()) {
            return redirect()->route('worker.login');
        }

        return $next($request);
    }
}