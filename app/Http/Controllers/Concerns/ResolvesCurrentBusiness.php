<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Shared helpers for resolving and guarding the authenticated user's business.
 *
 * Many controllers repeat the same "fetch the current business and bail out if
 * the user has not set one up yet" logic. Centralising it here keeps the route
 * name, error messages and ownership checks in a single place.
 */
trait ResolvesCurrentBusiness
{
    /**
     * The business owned by the currently authenticated user (if any).
     */
    protected function currentBusiness(): ?Business
    {
        return Auth::user()?->business;
    }

    /**
     * Redirect the user to the business setup flow.
     *
     * Used when an action requires a business but the user has not created one.
     */
    protected function redirectToBusinessSetup(): RedirectResponse
    {
        return redirect()->route('business.choose-type');
    }

    /**
     * Standard JSON response for API/AJAX endpoints when no business is found.
     */
    protected function businessNotFoundJson(string $message = 'No business found', int $status = 404): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }

    /**
     * Ensure the current business owns the given model, aborting with 404 when
     * there is no business or the model belongs to a different business.
     *
     * Returns the resolved business so callers can keep using it.
     */
    protected function ensureBusinessOwns($model, ?Business $business = null): Business
    {
        $business ??= $this->currentBusiness();

        if (!$business || $model->business_id !== $business->id) {
            abort(404);
        }

        return $business;
    }
}
