<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
 

public function show($id)
{
    $business = Business::findOrFail($id);
    return view('business', compact('business'));
}
}