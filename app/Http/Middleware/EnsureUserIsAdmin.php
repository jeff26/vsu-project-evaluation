<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in AND is an admin
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. Administrator privileges required.'
            ], 403);
        }

        return $next($request);
    }
}
