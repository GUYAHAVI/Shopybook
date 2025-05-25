<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;

class IndexController extends Controller
{
      public function index(Request $request)
{
    // Set default sorting values
    $sort = $request->get('sort', 'name');  // Default sort by name
    $order = $request->get('order', 'asc'); // Default ascending order

    // Get all active business types
    $businessTypes = Business::where('active', true)
                           ->select('business_type')
                           ->distinct()
                           ->orderBy('business_type')
                           ->pluck('business_type');
    
    // Group businesses by type
    $groupedBusinesses = [];
    foreach ($businessTypes as $type) {
        $groupedBusinesses[$type] = Business::where('business_type', $type)
                                          ->where('active', true)
                                          ->orderBy($sort, $order)
                                          ->take(4) // Limit to 4 per category
                                          ->get();
    }

    return view('index', [
        'groupedBusinesses' => $groupedBusinesses,
        'sort' => $sort,
        'order' => $order,
        // Include any other data your homepage needs
    ]);
}
}
