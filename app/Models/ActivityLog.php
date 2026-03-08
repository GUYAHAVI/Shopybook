<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'module',
        'action',
        'description',
        'meta',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    // ─── Static Logger ───────────────────────────────────────────────────────

    /**
     * Log an activity for the current authenticated user.
     *
     * @param string $businessId
     * @param string $module     e.g. 'pos', 'orders'
     * @param string $action     e.g. 'created', 'updated', 'deleted', 'viewed'
     * @param string $description
     * @param array  $meta
     */
    public static function record(
        string $businessId,
        string $module,
        string $action,
        string $description,
        array $meta = []
    ): void {
        try {
            $user = auth()->user();
            if (!$user) return;

            static::create([
                'business_id' => $businessId,
                'user_id'     => $user->id,
                'module'      => $module,
                'action'      => $action,
                'description' => $description,
                'meta'        => $meta ?: null,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the app
            \Log::warning('ActivityLog::record failed: ' . $e->getMessage());
        }
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForBusiness($query, string $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created'  => 'success',
            'updated'  => 'info',
            'deleted'  => 'danger',
            'viewed'   => 'secondary',
            'login'    => 'primary',
            'logout'   => 'warning',
            default    => 'secondary',
        };
    }
}
