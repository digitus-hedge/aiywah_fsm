<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage:  ->middleware('permission:invoice_panel')
 *         ->middleware('permission:assigned,any')   // allow 'rls' users too
 *
 * Safe methods (GET/HEAD/OPTIONS) need only access.
 * Anything that mutates additionally requires write access, so a read-only
 * role gets the page but is blocked on submit.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $key, string $mode = 'strict'): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // 'any' also admits filtered ('rls') users; 'strict' requires full access.
        $allowed = $mode === 'any'
            ? $user->hasAnyAccess($key)
            : $user->hasAccess($key);

        if (!$allowed) {
            abort(403, 'You do not have access to this page.');
        }

        if (!$request->isMethodSafe() && $user->isReadonly($key)) {
            abort(403, 'Your access to this page is read-only.');
        }

        return $next($request);
    }
}