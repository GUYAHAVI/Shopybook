<?php

namespace Tests\Unit\Services;

use App\Services\HostPinnacleSmsService;
use Illuminate\Support\Facades\Config;
use ReflectionMethod;
use Tests\TestCase;

class HostPinnacleSmsServiceTest extends TestCase
{
    private function makeService(): HostPinnacleSmsService
    {
        return new HostPinnacleSmsService;
    }

    private function formatPhone(HostPinnacleSmsService $service, string $phone)
    {
        $ref = new ReflectionMethod(HostPinnacleSmsService::class, 'formatPhoneNumber');
        $ref->setAccessible(true);

        return $ref->invoke($service, $phone);
    }

    public function test_is_configured_false_when_credentials_missing(): void
    {
        Config::set('services.hostpinnacle.user_id', null);
        Config::set('services.hostpinnacle.password', null);
        Config::set('services.hostpinnacle.sender_id', null);

        $this->assertFalse($this->makeService()->isConfigured());
    }

    public function test_is_configured_true_when_all_credentials_present(): void
    {
        Config::set('services.hostpinnacle.user_id', 'user');
        Config::set('services.hostpinnacle.password', 'secret');
        Config::set('services.hostpinnacle.sender_id', 'SHOPYBOOK');

        $this->assertTrue($this->makeService()->isConfigured());
    }

    public function test_send_sms_returns_error_when_not_configured(): void
    {
        Config::set('services.hostpinnacle.user_id', null);
        Config::set('services.hostpinnacle.password', null);
        Config::set('services.hostpinnacle.sender_id', null);

        $result = $this->makeService()->sendSms('0712345678', 'Hello');

        $this->assertFalse($result['success']);
        $this->assertSame('Missing credentials', $result['error']);
    }

    public function test_format_phone_converts_leading_zero_to_country_code(): void
    {
        $service = $this->makeService();
        $this->assertSame('254712345678', $this->formatPhone($service, '0712345678'));
    }

    public function test_format_phone_strips_plus_and_separators(): void
    {
        $service = $this->makeService();
        $this->assertSame('254712345678', $this->formatPhone($service, '+254 712 345 678'));
    }

    public function test_format_phone_prefixes_nine_digit_number(): void
    {
        $service = $this->makeService();
        $this->assertSame('254712345678', $this->formatPhone($service, '712345678'));
    }

    public function test_format_phone_returns_null_for_empty_input(): void
    {
        $service = $this->makeService();
        $this->assertNull($this->formatPhone($service, 'abc'));
    }

    public function test_format_phone_returns_null_for_too_short_number(): void
    {
        $service = $this->makeService();
        $this->assertNull($this->formatPhone($service, '12345'));
    }
}
