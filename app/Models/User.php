<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    /**
     * Get the user's owned business (they are the owner).
     */
    public function business()
    {
        return $this->hasOne(Business::class);
    }

    /**
     * Get all businesses the user owns.
     */
    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    /**
     * Get all business memberships for this user (as a team member).
     */
    public function businessMemberships()
    {
        return $this->hasMany(BusinessMember::class);
    }

    /**
     * Get the active membership for this user (if they are a team member).
     */
    public function activeMembership()
    {
        return $this->hasOne(BusinessMember::class)->where('status', 'active');
    }

    /**
     * Check if this user is the owner of the given business.
     */
    public function isOwnerOf(string $businessId): bool
    {
        if (array_key_exists('business', $this->relations)) {
            return $this->relations['business']?->id === $businessId;
        }
        return $this->businesses()->where('id', $businessId)->exists();
    }

    /**
     * Check if this user has a given module permission within their current business context.
     * Owners always have full access.
     *
     * @param  string  $module  e.g. 'pos', 'reports'
     * @param  string|null  $businessId  If null, uses the cached/active business
     */
    public function hasModulePermission(string $module, ?string $businessId = null): bool
    {
        // If user owns a business, they have all permissions
        $ownedBusiness = $this->relations['business'] ?? $this->business;
        if ($ownedBusiness) {
            return true;
        }

        // Team member path – check membership permissions
        $membership = $this->relations['activeMembership'] ?? $this->activeMembership;
        if (!$membership) {
            return false;
        }

        if ($businessId && $membership->business_id !== $businessId) {
            return false;
        }

        return $membership->hasPermission($module);
    }

    /**
     * Get this user's membership record for a specific business (if they are a member).
     */
    public function getMembershipFor(string $businessId): ?BusinessMember
    {
        return $this->businessMemberships()
            ->where('business_id', $businessId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
