<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'business_id', 'name', 'role', 'quote',
        'rating', 'is_approved', 'approved_at', 'approved_by', 'ip_address',
        'status',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rating'      => 'integer',
        'deleted_at'  => 'datetime',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopePlatform(Builder $query): Builder
    {
        return $query->where('type', 'platform');
    }

    public function scopeForBusiness(Builder $query, string $businessId): Builder
    {
        return $query->where('type', 'business')->where('business_id', $businessId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function approve(): void
    {
        $this->update([
            'status'      => 'approved',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status'      => 'rejected',
            'is_approved' => false,
        ]);
    }

    // ── Stars helper ──────────────────────────────────────────────────────

    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}
