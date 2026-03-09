<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Testimonial;

class IndexController extends Controller
{
      public function index(Request $request)
{
    $sort  = $request->get('sort', 'name');
    $order = $request->get('order', 'asc');

    $businessTypes = Business::where('active', true)
                           ->select('business_type')
                           ->distinct()
                           ->orderBy('business_type')
                           ->pluck('business_type');

    $groupedBusinesses = [];
    foreach ($businessTypes as $type) {
        $groupedBusinesses[$type] = Business::where('business_type', $type)
                                          ->where('active', true)
                                          ->orderBy($sort, $order)
                                          ->take(4)
                                          ->get();
    }

    $platformTestimonials = Testimonial::approved()->platform()->latest()->get();

    return view('index', [
        'groupedBusinesses'    => $groupedBusinesses,
        'sort'                 => $sort,
        'order'                => $order,
        'platformTestimonials' => $platformTestimonials,
    ]);
}
}
