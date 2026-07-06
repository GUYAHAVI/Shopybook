<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TwoFactorAuthServiceTest extends TestCase
{
    private TwoFactorAuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TwoFactorAuthService;
        Cache::flush();
    }

    private function makeUser(int $id = 1): User
    {
        $user = new User;
        $user->id = $id;

        return $user;
    }

    private function seedCode(User $user, string $action, string $code, $expiresAt, array $context = []): void
    {
        Cache::put("2fa_{$user->id}_{$action}", [
            'code' => $code,
            'expires_at' => $expiresAt,
            'context' => $context,
        ], now()->addMinutes(10));
    }

    public function test_generate_verification_code_is_six_digits(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $code = $this->service->generateVerificationCode();
            $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        }
    }

    public function test_verify_code_succeeds_with_valid_code(): void
    {
        $user = $this->makeUser();
        $this->seedCode($user, 'business_delete', '123456', now()->addMinutes(5), ['business_id' => 7]);

        $result = $this->service->verifyCode($user, 'business_delete', '123456');

        $this->assertTrue($result['success']);
        $this->assertSame(['business_id' => 7], $result['context']);
        // Code should be consumed after a successful verification.
        $this->assertFalse($this->service->hasPendingVerification($user, 'business_delete'));
    }

    public function test_verify_code_fails_when_no_code_present(): void
    {
        $result = $this->service->verifyCode($this->makeUser(), 'business_delete', '000000');

        $this->assertFalse($result['success']);
        $this->assertSame('Verification code has expired or is invalid.', $result['message']);
    }

    public function test_verify_code_fails_with_wrong_code(): void
    {
        $user = $this->makeUser();
        $this->seedCode($user, 'account_delete', '111111', now()->addMinutes(5));

        $result = $this->service->verifyCode($user, 'account_delete', '999999');

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid verification code.', $result['message']);
    }

    public function test_verify_code_fails_and_clears_when_expired(): void
    {
        $user = $this->makeUser();
        $this->seedCode($user, 'password_change', '222222', now()->subMinute());

        $result = $this->service->verifyCode($user, 'password_change', '222222');

        $this->assertFalse($result['success']);
        $this->assertSame('Verification code has expired.', $result['message']);
        $this->assertFalse($this->service->hasPendingVerification($user, 'password_change'));
    }

    public function test_pending_verification_helpers(): void
    {
        $user = $this->makeUser(3);
        $this->assertFalse($this->service->hasPendingVerification($user, 'admin_access'));

        $this->seedCode($user, 'admin_access', '333333', now()->addMinutes(5), ['scope' => 'all']);

        $this->assertTrue($this->service->hasPendingVerification($user, 'admin_access'));
        $this->assertSame(['scope' => 'all'], $this->service->getPendingVerificationContext($user, 'admin_access'));

        $this->service->clearPendingVerification($user, 'admin_access');
        $this->assertFalse($this->service->hasPendingVerification($user, 'admin_access'));
    }

    public function test_get_action_name_maps_known_actions(): void
    {
        $this->assertSame('Delete Business', TwoFactorAuthService::getActionName('business_delete'));
        $this->assertSame('Change Password', TwoFactorAuthService::getActionName('password_change'));
    }

    public function test_get_action_name_humanizes_unknown_actions(): void
    {
        // ucfirst + underscore replacement only capitalizes the first word.
        $this->assertSame('Some custom action', TwoFactorAuthService::getActionName('some_custom_action'));
    }
}
