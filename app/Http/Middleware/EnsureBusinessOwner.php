<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict a route to the OWNER of the active business.
 * Team members — even admins — are rejected. Use for owner-only
 * surfaces like business profile, team management and billing.
 * Runs after has.business (which injects the active business).
 */
class EnsureBusinessOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $business = $user->relationLoaded('business')
            ? $user->getRelation('business')
            : $user->business;

        if (!$business || $business->user_id !== $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Only the business owner can do that.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Only the business owner can access that page.');
        }

        return $next($request);
    }
}
