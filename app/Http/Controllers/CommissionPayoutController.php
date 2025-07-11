<?php

namespace App\Http\Controllers;

use App\Models\CommissionPayout;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommissionPayoutController extends Controller
{
    public function index()
    {
        $payouts = CommissionPayout::where('business_id', Auth::user()->business->id)->with('staff')->orderByDesc('paid_at')->get();
        return view('commissions.index', compact('payouts'));
    }

    public function create()
    {
        $staff = Staff::where('business_id', Auth::user()->business->id)->get();
        return view('commissions.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $data['business_id'] = Auth::user()->business->id;
        CommissionPayout::create($data);
        return redirect()->route('commissions.index')->with('success', 'Commission payout recorded successfully.');
    }

    public function show(CommissionPayout $commission)
    {
        $this->authorize('view', $commission);
        return view('commissions.show', compact('commission'));
    }

    public function edit(CommissionPayout $commission)
    {
        $this->authorize('update', $commission);
        $staff = Staff::where('business_id', Auth::user()->business->id)->get();
        return view('commissions.edit', compact('commission', 'staff'));
    }

    public function update(Request $request, CommissionPayout $commission)
    {
        $this->authorize('update', $commission);
        $data = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $commission->update($data);
        return redirect()->route('commissions.index')->with('success', 'Commission payout updated successfully.');
    }

    public function destroy(CommissionPayout $commission)
    {
        $this->authorize('delete', $commission);
        $commission->delete();
        return redirect()->route('commissions.index')->with('success', 'Commission payout deleted successfully.');
    }
} 