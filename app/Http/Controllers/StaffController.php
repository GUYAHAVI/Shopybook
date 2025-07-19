<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $staff = Staff::where('business_id', $business->id)
            ->orderBy('name')
            ->get();

        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        return view('staff.create');
    }

    public function store(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        Staff::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'role' => $validated['role'],
            'contact' => $validated['contact'],
            'commission_rate' => $validated['commission_rate'],
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member added successfully!');
    }

    public function show(Staff $staff)
    {
        $this->authorize('view', $staff);
        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $this->authorize('update', $staff);
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $this->authorize('update', $staff);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully!');
    }

    public function destroy(Staff $staff)
    {
        $this->authorize('delete', $staff);
        
        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member removed successfully!');
    }
}
