<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMember extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'invited_email',
        'name',
        'role',
        'permissions',
        'status',
        'invited_by',
        'invite_token',
        'invited_at',
        'joined_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'invited_at'  => 'datetime',
        'joined_at'   => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        // Business uses a string PK
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Check if this member has permission for a module.
     */
    public function hasPermission(string $module): bool
    {
        if (!$this->permissions) {
            return false;
        }
        return in_array($module, $this->permissions);
    }

    /**
     * Get the human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return config("rbac.role_labels.{$this->role}", ucfirst($this->role));
    }

    /**
     * Check if the member is pending (invited but not accepted yet).
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the member is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Apply default permissions for the given role.
     */
    public function applyRoleDefaults(): void
    {
        $this->permissions = config("rbac.role_defaults.{$this->role}", []);
        $this->save();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForBusiness($query, string $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
