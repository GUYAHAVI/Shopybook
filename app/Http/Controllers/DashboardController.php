<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // This is the main dashboard for the tenant
        // You can fetch tenant-specific data here
        return view('dashboard');
    }
}
