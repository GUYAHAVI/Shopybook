<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Services\Accounting\Ledger;
use App\Services\Accounting\ReportService;
use Illuminate\Http\Request;

/**
 * Financial reports. Every figure comes from the posted ledger (journal_lines) —
 * the same entries written automatically by sales, payments, stock, costs, etc.
 */
class ReportsController extends Controller
{
    public function __construct(private ReportService $reports)
    {
    }

    private function businessId(): string
    {
        return (string) auth()->user()->business->id;
    }

    private function range(Request $request): array
    {
        return [
            $request->input('start_date', now()->startOfMonth()->toDateString()),
            $request->input('end_date', now()->toDateString()),
        ];
    }

    public function index()
    {
        $businessId = $this->businessId();
        [$from, $to] = [now()->startOfMonth()->toDateString(), now()->toDateString()];

        return view('accounting.index', [
            'cash' => $this->reports->cashBook($businessId, $from, $to),
            'receivables' => $this->reports->aging($businessId, 'receivable', $to)->sum('total'),
            'payables' => $this->reports->aging($businessId, 'payable', $to)->sum('total'),
            'setupDone' => (bool) \App\Models\AccountingSetting::for($businessId)->setup_completed_at,
        ]);
    }

    public function profitLoss(Request $request)
    {
        [$from, $to] = $this->range($request);
        return view('accounting.profit-loss', [
            'report' => $this->reports->profitLoss($this->businessId(), $from, $to),
            'startDate' => $from, 'endDate' => $to,
        ]);
    }

    public function balanceSheet(Request $request)
    {
        $asOf = $request->input('as_of', now()->toDateString());
        return view('accounting.balance-sheet', [
            'report' => $this->reports->balanceSheet($this->businessId(), $asOf),
            'asOf' => $asOf,
        ]);
    }

    public function trialBalance(Request $request)
    {
        [$from, $to] = $this->range($request);
        return view('accounting.trial-balance', [
            'report' => $this->reports->trialBalance($this->businessId(), $from, $to),
            'startDate' => $from, 'endDate' => $to,
        ]);
    }

    public function statement(Request $request, int $account)
    {
        [$from, $to] = $this->range($request);
        return view('accounting.statement', array_merge(
            $this->reports->accountStatement($this->businessId(), $account, $from, $to),
            ['startDate' => $from, 'endDate' => $to]
        ));
    }

    /** One customer's or supplier's running statement — invoices, payments, balance. */
    public function party(Request $request, string $type, string $party)
    {
        abort_unless(in_array($type, ['customer', 'supplier']), 404);
        [$from, $to] = $this->range($request);
        $businessId = $this->businessId();
        $accountKey = $type === 'customer' ? 'receivable' : 'payable';
        $name = $type === 'customer'
            ? \App\Models\Customer::where('business_id', $businessId)->find($party)?->name
            : \App\Models\Supplier::where('business_id', $businessId)->find($party)?->name;

        return view('accounting.party', array_merge(
            $this->reports->partyStatement($businessId, $accountKey, $type, $party, $from, $to),
            ['title' => $name ?? ucfirst($type)." #{$party}", 'startDate' => $from, 'endDate' => $to]
        ));
    }

    public function money(Request $request)
    {
        [$from, $to] = $this->range($request);
        return view('accounting.money-book', [
            'report' => $this->reports->cashBook($this->businessId(), $from, $to),
            'startDate' => $from, 'endDate' => $to,
        ]);
    }

    public function aging(Request $request)
    {
        $asOf = $request->input('as_of', now()->toDateString());
        $businessId = $this->businessId();

        return view('accounting.aging', [
            'receivables' => $this->reports->aging($businessId, 'receivable', $asOf),
            'payables' => $this->reports->aging($businessId, 'payable', $asOf),
            'customers' => \App\Models\Customer::where('business_id', $businessId)->pluck('name', 'id'),
            'suppliers' => \App\Models\Supplier::where('business_id', $businessId)->pluck('name', 'id'),
            'asOf' => $asOf,
        ]);
    }

    public function stockValuation()
    {
        return view('accounting.stock-valuation', [
            'rows' => $this->reports->stockValuation($this->businessId()),
        ]);
    }

    public function vat(Request $request)
    {
        [$from, $to] = $this->range($request);
        return view('accounting.vat', [
            'report' => $this->reports->vatSummary($this->businessId(), $from, $to),
            'startDate' => $from, 'endDate' => $to,
        ]);
    }
}
