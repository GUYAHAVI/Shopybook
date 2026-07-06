<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisit
{
    /**
     * Path prefixes that should never be tracked (assets, health checks, polling endpoints).
     */
    protected array $excludedPrefixes = [
        'up',
        'icons',
        'storage',
        'build',
        'manifest.json',
        'favicon.ico',
        'notifications/unread-count',
        'admin/analytics',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        if ($this->shouldTrack($request)) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            try {
                $user = auth()->user();

                PageVisit::create([
                    'user_id'     => $user?->id,
                    'business_id' => $user && $user->relationLoaded('business') ? $user->business?->id : null,
                    'path'        => substr('/' . ltrim($request->path(), '/'), 0, 500),
                    'route_name'  => $request->route()?->getName(),
                    'method'      => $request->method(),
                    'status_code' => $response->getStatusCode(),
                    'duration_ms' => $durationMs,
                    'session_id'  => $request->hasSession() ? $request->session()->getId() : null,
                    'ip_address'  => $request->ip(),
                    'user_agent'  => substr((string) $request->userAgent(), 0, 255),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('TrackPageVisit failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    protected function shouldTrack(Request $request): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        foreach ($this->excludedPrefixes as $prefix) {
            if ($request->is($prefix) || $request->is($prefix . '/*')) {
                return false;
            }
        }

        return true;
    }
}
