<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function destroy(Staff $staff, Request $request)
    {
        $business = Auth::user()->business;
        
        // Check if the staff belongs to the user's business
        if ($staff->business->id !== $business->id) {
            return redirect()->route('staff.index')->with('error', 'Unauthorized action.');
        }

        // Verify password
        if (!Hash::check($request->input('password'), auth()->user()->getAuthPassword())) {
            return back()->with('error', 'Invalid password. Staff deletion canceled.');
        }

        try {
            // Use null coalescing operator in case the property doesn't exist
            $staffName = $staff->name ?? ('Staff #' . $staff->getKey());
            $staff->delete();

            return redirect()->route('staff.index')
                ->with('success', "Staff member '{$staffName}' has been successfully removed.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete staff member: ' . $e->getMessage());
        }
    }

    public function commissionReports(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $staff = Staff::where('business_id', $business->id)
            ->with(['serviceItems' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('serviceBooking', function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                })
                ->with(['service', 'serviceBooking']);
            }])
            ->get();

        // Calculate commission summaries
        $staffCommissions = $staff->map(function ($staffMember) use ($startDate, $endDate) {
            $serviceItems = $staffMember->serviceItems;
            $totalCommission = $serviceItems->sum('commission_amount');
            $serviceCount = $serviceItems->count();

            return [
                'staff' => $staffMember,
                'total_commission' => $totalCommission,
                'service_count' => $serviceCount,
                'service_items' => $serviceItems,
                'today_commission' => $staffMember->todayCommission,
                'this_month_commission' => $staffMember->thisMonthCommission,
            ];
        });

        $totalCommissions = $staffCommissions->sum('total_commission');

        return view('commission-reports', compact(
            'staffCommissions', 
            'totalCommissions', 
            'startDate', 
            'endDate'
        ));
    }
}
