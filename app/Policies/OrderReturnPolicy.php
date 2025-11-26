<?php

namespace App\Policies;

use App\Models\OrderReturn;
use App\Models\User;

class OrderReturnPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderReturn $orderReturn): bool
    {
        return $user->business && $user->business->id === $orderReturn->business_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderReturn $orderReturn): bool
    {
        return $user->business && $user->business->id === $orderReturn->business_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderReturn $orderReturn): bool
    {
        return $user->business && $user->business->id === $orderReturn->business_id;
    }
}
