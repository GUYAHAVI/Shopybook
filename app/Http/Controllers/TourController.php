<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{
    /**
     * Mark a page tour as completed (or dismissed) for the current user.
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'tour' => 'required|string|max:100',
        ]);

        $tour = $validated['tour'];

        if (!array_key_exists($tour, config('tours', []))) {
            return response()->json(['success' => false, 'message' => 'Unknown tour'], 404);
        }

        Auth::user()->markTourCompleted($tour);

        return response()->json(['success' => true]);
    }

    /**
     * Reset all completed tours so the advisor offers them again.
     */
    public function reset()
    {
        Auth::user()->forceFill(['completed_tours' => []])->save();

        return response()->json(['success' => true]);
    }
}
