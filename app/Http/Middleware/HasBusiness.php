<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasBusiness
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // app/Http/Middleware/HasBusiness.php
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->business) {
            return redirect()->route('business.create')
                ->with('error', 'You need to create a business profile first');
        }

        return $next($request);
    }
}
