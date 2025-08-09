<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
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
    public function view(User $user, Order $order): bool
    {
        \Log::info('OrderPolicy view called', [
            'user_id' => $user->id,
            'user_business_id' => $user->business->id ?? 'null',
            'order_id' => $order->id,
            'order_business_id' => $order->business_id,
            'result' => $user->business && $user->business->id === $order->business_id
        ]);
        
        return $user->business && $user->business->id === $order->business_id;
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
    public function update(User $user, Order $order): bool
    {
        \Log::info('OrderPolicy update called', [
            'user_id' => $user->id,
            'user_business_id' => $user->business->id ?? 'null',
            'order_id' => $order->id,
            'order_business_id' => $order->business_id,
            'result' => $user->business && $user->business->id === $order->business_id
        ]);
        
        return $user->business && $user->business->id === $order->business_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->business && $user->business->id === $order->business_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->business && $user->business->id === $order->business_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->business && $user->business->id === $order->business_id;
    }
}
