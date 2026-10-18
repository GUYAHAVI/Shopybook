<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Platform-wide (super admin) settings stored as key/value pairs.
 */
class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected const CACHE_KEY = 'platform_settings:all';

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever(self::CACHE_KEY, fn() => static::pluck('value', 'key')->all());

        return $all[$key] ?? $default;
    }

    public static function set(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Where Shopybook subscription payments settle (bank deposit via M-Pesa paybill).
     *
     * @return array{gateway: string, bank: ?string, bank_name: ?string, paybill: ?string, account: ?string, ready: bool}
     */
    public static function subscriptionDestination(): array
    {
        $bank    = static::get('subscription_mpesa_bank');
        $account = static::get('subscription_mpesa_account');
        $paybill = $bank ? config("mpesa_banks.{$bank}.paybill") : null;

        return [
            'gateway'   => static::get('subscription_gateway', 'paystack'),
            'bank'      => $bank,
            'bank_name' => $bank ? config("mpesa_banks.{$bank}.name") : null,
            'paybill'   => $paybill,
            'account'   => $account,
            'ready'     => static::get('subscription_gateway') === 'daraja' && $paybill && $account,
        ];
    }
}
