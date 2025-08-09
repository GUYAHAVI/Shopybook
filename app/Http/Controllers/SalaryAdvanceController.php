<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SalaryAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $salaryAdvances = SalaryAdvance::with(['staff', 'approvedBy'])
            ->forBusiness($business->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('salary-advances.index', compact('salaryAdvances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $staff = Staff::where('business_id', $business->id)
            ->whereNotNull('salary')
            ->where('salary', '>', 0)
            ->get();

        return view('salary-advances.create', compact('staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date|before_or_equal:today',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify staff belongs to business
        $staff = Staff::where('id', $request->staff_id)
            ->where('business_id', $business->id)
            ->first();

        if (!$staff) {
            return back()->withErrors(['staff_id' => 'Invalid staff member selected.']);
        }

        // Check if staff can take this advance amount
        if (!$staff->canTakeAdvance($request->amount)) {
            $available = $staff->available_advance_amount;
            return back()->withErrors([
                'amount' => "Amount exceeds available advance limit. Available: KSh " . number_format($available, 2)
            ]);
        }

        DB::transaction(function () use ($request, $business, $staff) {
            SalaryAdvance::create([
                'business_id' => $business->id,
                'staff_id' => $request->staff_id,
                'amount' => $request->amount,
                'advance_date' => $request->advance_date,
                'status' => 'pending',
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            // Clear staff calculated attributes cache
            $staff->clearCalculatedAttributesCache();
        });

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalaryAdvance $salaryAdvance)
    {
        $business = Auth::user()->business;
        if (!$business || $salaryAdvance->business_id !== $business->id) {
            abort(404);
        }

        $salaryAdvance->load(['staff', 'approvedBy']);
        
        return view('salary-advances.show', compact('salaryAdvance'));
    }

    /**
     * Approve a salary advance with password verification.
     */
    public function approve(SalaryAdvance $salaryAdvance, Request $request)
    {
        $business = Auth::user()->business;
        if (!$business || $salaryAdvance->business_id !== $business->id) {
            abort(404);
        }

        if ($salaryAdvance->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending advances can be approved.']);
        }

        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Invalid password. Please try again.']);
        }

        $salaryAdvance->approve(Auth::id());

        // Clear any cached relationships and calculated attributes
        $salaryAdvance->staff->clearCalculatedAttributesCache();

        return back()->with('success', 'Salary advance approved successfully.');
    }

    /**
     * Mark a salary advance as paid with password verification.
     */
    public function markAsPaid(SalaryAdvance $salaryAdvance, Request $request)
    {
        $business = Auth::user()->business;
        if (!$business || $salaryAdvance->business_id !== $business->id) {
            abort(404);
        }

        if ($salaryAdvance->status !== 'approved') {
            return back()->withErrors(['error' => 'Only approved advances can be marked as paid.']);
        }

        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Invalid password. Please try again.']);
        }

        $salaryAdvance->markAsPaid();

        // Clear any cached relationships and calculated attributes
        $salaryAdvance->staff->clearCalculatedAttributesCache();

        return back()->with('success', 'Salary advance marked as paid successfully.');
    }

    /**
     * Cancel a salary advance with password verification.
     */
    public function cancel(SalaryAdvance $salaryAdvance, Request $request)
    {
        $business = Auth::user()->business;
        if (!$business || $salaryAdvance->business_id !== $business->id) {
            abort(404);
        }

        if (!in_array($salaryAdvance->status, ['pending', 'approved'])) {
            return back()->withErrors(['error' => 'Only pending or approved advances can be cancelled.']);
        }

        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Invalid password. Please try again.']);
        }

        $salaryAdvance->cancel();

        // Clear any cached relationships and calculated attributes
        $salaryAdvance->staff->clearCalculatedAttributesCache();

        return back()->with('success', 'Salary advance cancelled successfully.');
    }

    /**
     * Show staff advance summary
     */
    public function staffSummary()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $staff = Staff::with([
                'salaryAdvances' => function ($query) {
                    $query->orderBy('advance_date', 'desc');
                },
                'serviceItems' // Load service items for commission calculations
            ])
            ->where('business_id', $business->id)
            ->whereNotNull('salary')
            ->where('salary', '>', 0)
            ->get();

        return view('salary-advances.staff-summary', compact('staff'));
    }

    /**
     * Remove the specified resource from storage with password verification.
     */
    public function destroy(SalaryAdvance $salaryAdvance, Request $request)
    {
        $business = Auth::user()->business;
        if (!$business || $salaryAdvance->business_id !== $business->id) {
            abort(404);
        }

        if ($salaryAdvance->status === 'paid') {
            return back()->withErrors(['error' => 'Cannot delete paid advances.']);
        }

        // Verify password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Invalid password. Please try again.']);
        }

        $salaryAdvance->delete();

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance deleted successfully.');
    }
}
