<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BusinessMember;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:  ->middleware('permission:pos')
     *   or multiple:    ->middleware('permission:products,orders')
     *
     * Pass if user has ANY of the listed modules.
     * Owners always pass. Suspended members are always rejected.
     */
    public function handle(Request $request, Closure $next, string ...$modules): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Business owners always have full access
        $ownedBusiness = $user->relationLoaded('business')
            ? $user->getRelation('business')
            : $user->business;

        if ($ownedBusiness && $ownedBusiness->user_id === $user->id) {
            return $next($request);
        }

        // Team member – must be active (not suspended) AND have at least one required permission
        $membership = $user->relationLoaded('activeMembership')
            ? $user->getRelation('activeMembership')
            : $user->activeMembership;

        if (!$membership || $membership->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Your account access has been suspended or removed.'], 403);
            }
            return redirect()->route('login')
                ->with('error', 'Your account access has been suspended or removed. Please contact the business owner.');
        }

        // Check module permissions
        foreach ($modules as $module) {
            if ($membership->hasPermission($module)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'You do not have permission to access this module.'], 403);
        }

        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access that module.');
    }
}
