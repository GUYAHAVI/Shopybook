<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
{
    $business = auth()->user()->business; // or however you get the business
    return view('dashboard', compact('business'));
}
}
