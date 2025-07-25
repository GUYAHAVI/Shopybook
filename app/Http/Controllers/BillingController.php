<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    /**
     * Show upgrade plans page
     */
    public function upgrade()
    {
        $user = Auth::user();
        $business = $user->business;
        
        return view('billing.upgrade', compact('business'));
    }

    /**
     * Handle plan upgrade
     */
    public function processUpgrade(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium,enterprise'
        ]);

        $user = Auth::user();
        $business = $user->business;
        $plan = $request->input('plan');

        // Update business plan
        $business->update([
            'plan' => $plan,
            'upgraded_at' => now()
        ]);

        return redirect()->route('billing.upgrade')
            ->with('success', "Successfully upgraded to {$plan} plan!");
    }

    /**
     * Show billing dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $business = $user->business;
        
        return view('billing.dashboard', compact('business'));
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $user = Auth::user();
        $business = $user->business;

        $business->update([
            'plan' => 'free',
            'cancelled_at' => now()
        ]);

        return redirect()->route('billing.dashboard')
            ->with('success', 'Subscription cancelled successfully.');
    }
}
