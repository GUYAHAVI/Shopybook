<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Silent bot protection using honeypot + timing.
 *
 * Honeypot: a hidden field named `website_url` is added to forms.
 * Humans never fill it (it's hidden + aria-hidden), but bots fill every field.
 * If the field has a value, the submission is silently rejected.
 *
 * Timing: a hidden `form_started_at` timestamp is set when the form loads.
 * If the form is submitted in under 2 seconds, it's almost certainly a bot.
 * Humans take at least a few seconds to read and fill a form.
 */
class Honeypot
{
    /**
     * Honeypot field name — looks tempting to bots but is hidden from humans.
     */
    protected string $honeypotField = 'website_url';

    /**
     * Timing field name.
     */
    protected string $timingField = 'form_started_at';

    /**
     * Minimum seconds a human would take to fill the form.
     */
    protected int $minSeconds = 2;

    public function handle(Request $request, Closure $next)
    {
        // Only check POST/PUT/PATCH requests
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        // Skip if the request doesn't have either field (e.g., API calls, AJAX without forms)
        if (!$request->has($this->honeypotField) && !$request->has($this->timingField)) {
            return $next($request);
        }

        // Check 1: honeypot field must be empty
        if ($request->filled($this->honeypotField)) {
            Log::warning('Honeypot triggered', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'user_agent' => $request->userAgent(),
                'honeypot_value' => $request->input($this->honeypotField),
            ]);
            // Pretend success so the bot doesn't learn
            return response()->json(['success' => true], 200);
        }

        // Check 2: timing — form must take at least minSeconds to submit
        $startedAt = $request->input($this->timingField);
        if ($startedAt) {
            try {
                $elapsed = now()->diffInSeconds(\Carbon\Carbon::parse($startedAt));
                if ($elapsed < $this->minSeconds) {
                    Log::warning('Form submitted too fast (bot?)', [
                        'ip' => $request->ip(),
                        'path' => $request->path(),
                        'elapsed_seconds' => $elapsed,
                    ]);
                    return response()->json(['success' => true], 200);
                }
            } catch (\Throwable $e) {
                // Invalid timestamp — let it through, don't break real users
            }
        }

        return $next($request);
    }
}
