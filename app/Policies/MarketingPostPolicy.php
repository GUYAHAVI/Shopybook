<?php

namespace App\Policies;

use App\Models\MarketingPost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MarketingPostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->business !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MarketingPost $marketingPost): bool
    {
        return $user->business->id === $marketingPost->business->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->business !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MarketingPost $marketingPost): bool
    {
        return $user->business->id === $marketingPost->business->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MarketingPost $marketingPost): bool
    {
        return $user->business->id === $marketingPost->business->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MarketingPost $marketingPost): bool
    {
        return $user->business->id === $marketingPost->business->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MarketingPost $marketingPost): bool
    {
        return $user->business->id === $marketingPost->business->id;
    }
}
