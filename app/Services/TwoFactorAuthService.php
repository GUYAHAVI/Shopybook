<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TwoFactorAuthService
{
    /**
     * Generate a 6-digit verification code
     */
    public function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send verification code via email
     */
    public function sendVerificationCode(User $user, string $action, array $context = []): bool
    {
        try {
            $code = $this->generateVerificationCode();
            $expiresAt = Carbon::now()->addMinutes(10); // 10 minutes expiry
            
            // Store the verification code in cache
            $cacheKey = "2fa_{$user->id}_{$action}";
            Cache::put($cacheKey, [
                'code' => $code,
                'expires_at' => $expiresAt,
                'context' => $context
            ], $expiresAt);
            
            // Send email with verification code
            Mail::send('emails.two-factor-verification', [
                'user' => $user,
                'code' => $code,
                'action' => $action,
                'context' => $context,
                'expires_at' => $expiresAt
            ], function ($message) use ($user, $action) {
                $message->to($user->email)
                        ->subject("Security Verification Required - {$action}")
                        ->priority(1);
            });
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send 2FA code', [
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verify the provided code
     */
    public function verifyCode(User $user, string $action, string $code): array
    {
        $cacheKey = "2fa_{$user->id}_{$action}";
        $cached = Cache::get($cacheKey);
        
        if (!$cached) {
            return [
                'success' => false,
                'message' => 'Verification code has expired or is invalid.'
            ];
        }
        
        if ($cached['code'] !== $code) {
            return [
                'success' => false,
                'message' => 'Invalid verification code.'
            ];
        }
        
        if (Carbon::now()->isAfter($cached['expires_at'])) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Verification code has expired.'
            ];
        }
        
        // Code is valid - remove it from cache
        Cache::forget($cacheKey);
        
        return [
            'success' => true,
            'message' => 'Verification successful.',
            'context' => $cached['context'] ?? []
        ];
    }

    /**
     * Check if user has a pending verification for an action
     */
    public function hasPendingVerification(User $user, string $action): bool
    {
        $cacheKey = "2fa_{$user->id}_{$action}";
        return Cache::has($cacheKey);
    }

    /**
     * Get pending verification context
     */
    public function getPendingVerificationContext(User $user, string $action): ?array
    {
        $cacheKey = "2fa_{$user->id}_{$action}";
        $cached = Cache::get($cacheKey);
        
        return $cached['context'] ?? null;
    }

    /**
     * Clear pending verification
     */
    public function clearPendingVerification(User $user, string $action): void
    {
        $cacheKey = "2fa_{$user->id}_{$action}";
        Cache::forget($cacheKey);
    }

    /**
     * Get human-readable action name
     */
    public static function getActionName(string $action): string
    {
        $actions = [
            'business_delete' => 'Delete Business',
            'business_edit' => 'Edit Business Profile',
            'account_delete' => 'Delete Account',
            'password_change' => 'Change Password',
            'email_change' => 'Change Email Address',
            'payment_method_add' => 'Add Payment Method',
            'admin_access' => 'Admin Access',
        ];
        
        return $actions[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }
}
