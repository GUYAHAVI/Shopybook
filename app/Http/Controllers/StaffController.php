<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentBusiness;
use App\Models\Staff;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    use ResolvesCurrentBusiness;

    public function index()
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $staff = Staff::where('business_id', $business->id)
            ->orderBy('name')
            ->get();

        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        return view('staff.create');
    }

    public function store(Request $request)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'salary' => 'nullable|integer|min:0',
        ]);

        Staff::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'role' => $validated['role'],
            'contact' => $validated['contact'],
            'salary' => $validated['salary'],
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
            'salary' => 'nullable|integer|min:0',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully!');
    }

    public function destroy(Staff $staff, Request $request)
    {
        $business = $this->currentBusiness();
        
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
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $staffCommissions = Staff::where('business_id', $business->id)
            ->with(['serviceItems' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($staff) {
                $totalCommission = $staff->serviceItems->sum('amount');
                $serviceCount = $staff->serviceItems->count();
                
                return [
                    'staff' => $staff,
                    'total_commission' => $totalCommission,
                    'service_count' => $serviceCount,
                    'salary' => $staff->salary ?? 0,
                    'total_earnings' => ($staff->salary ?? 0) + $totalCommission,
                ];
            });

        $totalCommissions = $staffCommissions->sum('total_commission');
        $totalEarnings = $staffCommissions->sum('total_earnings');

        return view('commission-reports', compact(
            'staffCommissions',
            'totalCommissions',
            'totalEarnings',
            'startDate',
            'endDate'
        ));
    }

    public function salaryCalculations(Request $request)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $selectedStaffId = $request->get('staff_id');
        
        $startDate = \Carbon\Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = Staff::where('business_id', $business->id);
        
        if ($selectedStaffId) {
            $query->where('id', $selectedStaffId);
        }

        $allStaff = Staff::where('business_id', $business->id)->get();

        $salaryCalculations = $query->get()->map(function ($staff) use ($startDate, $endDate) {
            // Get commissions for the month
            $commissions = $staff->serviceItems()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');
            
            $servicesCount = $staff->serviceItems()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // Get salary advances for the month
            $advances = $staff->salaryAdvances()
                ->whereBetween('advance_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->sum('amount');
            
            $advancesCount = $staff->salaryAdvances()
                ->whereBetween('advance_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->count();

            // Calculate net salary
            $baseSalary = $staff->salary ?? 0;
            $netSalary = $baseSalary + $commissions - $advances;

            return (object) [
                'staff' => $staff,
                'base_salary' => $baseSalary,
                'commissions' => $commissions,
                'services_count' => $servicesCount,
                'advances' => $advances,
                'advances_count' => $advancesCount,
                'net_salary' => $netSalary,
            ];
        });

        $totalNetSalaries = $salaryCalculations->sum('net_salary');
        $totalCommissions = $salaryCalculations->sum('commissions');
        $totalAdvances = $salaryCalculations->sum('advances');

        return view('staff.salary-calculations', compact(
            'salaryCalculations',
            'allStaff',
            'selectedMonth',
            'selectedStaffId',
            'totalNetSalaries',
            'totalCommissions',
            'totalAdvances'
        ));
    }

    public function salaryDetails(Request $request, $staff)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        // $staff is already the Staff model instance from route model binding
        if ($staff->business_id !== $business->id) {
            abort(404);
        }

        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $startDate = \Carbon\Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get detailed commission breakdown
        $commissionDetails = $staff->serviceItems()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['serviceBooking', 'service'])
            ->get();

        // Get detailed advances breakdown
        $advanceDetails = $staff->salaryAdvances()
            ->whereBetween('advance_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->get();

        $baseSalary = $staff->salary ?? 0;
        $totalCommissions = $commissionDetails->sum('amount');
        $totalAdvances = $advanceDetails->sum('amount');
        $netSalary = $baseSalary + $totalCommissions - $totalAdvances;

        return view('staff.salary-details', compact(
            'staff',
            'selectedMonth',
            'baseSalary',
            'commissionDetails',
            'advanceDetails',
            'totalCommissions',
            'totalAdvances',
            'netSalary'
        ));
    }
}
