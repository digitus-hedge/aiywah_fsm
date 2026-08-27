<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SeOrHopSe
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $role = optional($user?->role)->code;

        $allowed = $role === 'SE' || ($role === 'HP' && $user->is_se_enabled);

        if (! $allowed) {
            abort(403, 'You do not have Service Engineer access.');
        }

        return $next($request);
    }
}