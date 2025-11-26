<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TwoFactorAuthService;
use Illuminate\Support\Facades\Log;

class TwoFactorAuthController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show the 2FA verification form
     */
    public function showVerificationForm(Request $request)
    {
        $action = $request->query('action');
        $context = $request->query('context', []);
        
        if (!$action) {
            return redirect()->back()->with('error', 'Invalid verification request.');
        }

        $user = Auth::user();
        
        // Check if there's already a pending verification
        if (!$this->twoFactorService->hasPendingVerification($user, $action)) {
            return redirect()->back()->with('error', 'No pending verification found.');
        }

        return view('auth.two-factor-verification', [
            'action' => $action,
            'actionName' => $this->twoFactorService->getActionName($action),
            'context' => $context
        ]);
    }

    /**
     * Send verification code
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'context' => 'nullable|array'
        ]);

        $user = Auth::user();
        $action = $request->input('action');
        $context = $request->input('context', []);

        // Send verification code
        $success = $this->twoFactorService->sendVerificationCode($user, $action, $context);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email address.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify the code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $action = $request->input('action');
        $code = $request->input('code');

        $result = $this->twoFactorService->verifyCode($user, $action, $code);

        if ($result['success']) {
            // Store verification in session for the next request
            session(['2fa_verified_' . $action => true]);
            session(['2fa_context_' . $action => $result['context']]);

            Log::info('2FA verification successful', [
                'user_id' => $user->id,
                'action' => $action,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification successful!',
                'redirect' => $this->getRedirectUrl($action, $result['context'])
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    /**
     * Resend verification code
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'context' => 'nullable|array'
        ]);

        $user = Auth::user();
        $action = $request->input('action');
        $context = $request->input('context', []);

        // Clear any existing verification
        $this->twoFactorService->clearPendingVerification($user, $action);

        // Send new code
        $success = $this->twoFactorService->sendVerificationCode($user, $action, $context);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'New verification code sent to your email address.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }

    /**
     * Cancel verification
     */
    public function cancelVerification(Request $request)
    {
        $request->validate([
            'action' => 'required|string'
        ]);

        $user = Auth::user();
        $action = $request->input('action');

        $this->twoFactorService->clearPendingVerification($user, $action);

        Log::info('2FA verification cancelled', [
            'user_id' => $user->id,
            'action' => $action,
            'ip' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification cancelled.',
            'redirect' => route('dashboard')
        ]);
    }

    /**
     * Get redirect URL based on action
     */
    private function getRedirectUrl(string $action, array $context = []): string
    {
        switch ($action) {
            case 'business_delete':
                // After 2FA verification for deletion, proceed with actual deletion
                if (isset($context['business_id'])) {
                    $business = \App\Models\Business::find($context['business_id']);
                    if ($business && $business->user_id === auth()->id()) {
                        // Store the business ID in session for the destroy method
                        session(['pending_business_deletion_id' => $business->id]);
                        return route('business.destroy', $business);
                    }
                }
                return route('business.edit');
            case 'business_edit':
                return route('business.edit');
            case 'account_delete':
                return route('dashboard');
            default:
                return route('dashboard');
        }
    }
}
