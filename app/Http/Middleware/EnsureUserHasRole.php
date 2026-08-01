<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$codes): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $userCode = strtoupper((string) optional($user->role)->code);
        $allowed  = array_map('strtoupper', $codes);

        if (! in_array($userCode, $allowed, true)) {
            abort(403, 'This dashboard is not available for your role.');
        }

        return $next($request);
    }
}