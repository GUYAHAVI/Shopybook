<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{
    public function showForm()
    {
        return view('auth.business-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255|unique:tenants,id',
            'email' => 'required|email|unique:users,email',
            
            'password' => 'required|string|confirmed|min:8',
            'category' => 'required|string|max:255',
        ]);

        $slug = Str::slug($request->business_name);

        // 1. Create the tenant
        $tenant = Tenant::create([
            'id' => $slug,
            'slug' => $slug,
            'data' => [
                'category' => $request->category,
                'email' => $request->email,
            ]
        ]);

        // 2. Set a domain/path (if you're using path-based, this might be optional)
        $tenant->domains()->create([
            'domain' => 'localhost/' . $slug // for local dev; change to shopybook.co.ke/$slug later
        ]);

        // 3. Run code inside tenant's DB
        $tenant->run(function () use ($request) {
            \App\Models\User::create([
                'name' => $request->business_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'owner', // optional
            ]);
        });

        return redirect()->to('/' . $slug . '/login')->with('success', 'Business registered! Please log in.');
    }
}

