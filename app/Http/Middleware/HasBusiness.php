<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()->business) {
            return redirect()->route('business.create')
                ->with('error', 'You need to create a business profile first');
        }

        return $next($request);
    }
}