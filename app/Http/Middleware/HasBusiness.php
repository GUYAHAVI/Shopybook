<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BusinessMember;

class HasBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ── Path 1: User owns a business ──────────────────────────────────────
        $business = $user->business;

        // ── Path 2: User is a team member of another business ─────────────────
        if (!$business) {
            $membership = BusinessMember::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('business')
                ->first();

            if ($membership && $membership->business) {
                $business = $membership->business;
                // Cache the membership on the user so hasModulePermission() works
                $user->setRelation('activeMembership', $membership);
            }
        }

        if (!$business) {
            return redirect()->route('business.create')
                ->with('error', 'You need to create a business profile first');
        }

        // Inject the resolved business into the user's Eloquent relation cache.
        // This means auth()->user()->business works transparently for all controllers.
        $user->setRelation('business', $business);

        return $next($request);
    }
}