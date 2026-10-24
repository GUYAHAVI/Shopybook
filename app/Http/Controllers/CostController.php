<?php

namespace App\Http\Controllers;

use App\Exceptions\Accounting\LedgerException;
use App\Models\Cost;
use App\Services\Accounting\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CostController extends Controller
{
    public function index()
    {
        $costs = Cost::where('business_id', Auth::user()->business->id)->orderByDesc('date')->get();
        return view('costs.index', compact('costs'));
    }

    public function create(Ledger $ledger)
    {
        $moneyAccounts = $ledger->moneyAccounts(Auth::user()->business->id);
        return view('costs.create', compact('moneyAccounts'));
    }

    public function store(Request $request, Ledger $ledger)
    {
        $data = $this->validated($request);
        $data['business_id'] = Auth::user()->business->id;

        try {
            DB::transaction(fn () => $ledger->postCost(Cost::create($data)));
        } catch (LedgerException $e) {
            return back()->withInput()->withErrors(['date' => $e->getMessage()]);
        }

        return redirect()->route('costs.index')->with('success', 'Cost recorded successfully.');
    }

    public function show(Cost $cost)
    {
        $this->authorize('view', $cost);
        return view('costs.show', compact('cost'));
    }

    /** Settle a monthly salary cost: clears Salaries Payable against a money account. */
    public function markSalaryPaid(Request $request, Cost $cost, Ledger $ledger)
    {
        $this->authorize('update', $cost);
        if ($cost->type !== 'salary' || $cost->salary_paid_at) {
            return back();
        }
        $data = $request->validate(['paid_from_account_id' => ['nullable', Ledger::moneyAccountRule($cost->business_id)]]);

        DB::transaction(function () use ($cost, $ledger, $data) {
            $ledger->postSalariesPaid($cost, $data['paid_from_account_id'] ?? 'cash');
            $cost->update(['salary_paid_at' => now()]);
        });

        return back()->with('success', 'Salaries marked as paid.');
    }

    public function edit(Cost $cost, Ledger $ledger)
    {
        $this->authorize('update', $cost);
        $moneyAccounts = $ledger->moneyAccounts(Auth::user()->business->id);
        return view('costs.edit', compact('cost', 'moneyAccounts'));
    }

    public function update(Request $request, Cost $cost, Ledger $ledger)
    {
        $this->authorize('update', $cost);
        $data = $this->validated($request);

        try {
            DB::transaction(function () use ($cost, $data, $ledger) {
                $cost->update($data);
                $ledger->repostCost($cost);
            });
        } catch (LedgerException $e) {
            return back()->withInput()->withErrors(['date' => $e->getMessage()]);
        }

        return redirect()->route('costs.index')->with('success', 'Cost updated successfully.');
    }

    public function destroy(Cost $cost, Ledger $ledger)
    {
        $this->authorize('delete', $cost);

        try {
            DB::transaction(function () use ($cost, $ledger) {
                $ledger->reverse($cost);
                $cost->delete();
            });
        } catch (LedgerException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('costs.index')->with('success', 'Cost deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:utility,product,rent,water,misc,activity,renovation,salary,other',
            'amount' => 'required|numeric|min:0',
            'paid_from_account_id' => ['nullable', 'integer', Ledger::moneyAccountRule(Auth::user()->business->id)],
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);
    }
}
