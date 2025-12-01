<?php

namespace App\Policies;

use App\Models\ProductConversion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductConversionPolicy
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
    public function view(User $user, ProductConversion $conversion): bool
    {
        return $user->business->id === $conversion->business_id;
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
    public function update(User $user, ProductConversion $conversion): bool
    {
        return $user->business->id === $conversion->business_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductConversion $conversion): bool
    {
        return $user->business->id === $conversion->business_id;
    }
}






