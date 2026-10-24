<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountingSetting;
use App\Models\JournalEntry;
use App\Services\Accounting\BackfillService;
use App\Services\Accounting\Ledger;
use App\Services\Accounting\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * One-time setup: existing businesses either rebuild history into a draft batch
 * they review, or start fresh with opening balances on a chosen date.
 */
class SetupController extends Controller
{
    public function index()
    {
        $settings = AccountingSetting::for(auth()->user()->business->id);
        if ($settings->setup_completed_at) {
            return redirect()->route('accounting.index');
        }
        return view('accounting.setup', ['hasDraft' => false]);
    }

    /** Rebuild history into a draft batch, then show the review screen. */
    public function backfill(BackfillService $backfill)
    {
        $result = $backfill->run(auth()->user()->business->id);
        session(['accounting.backfill_batch' => $result['batch_id']]);

        return redirect()->route('accounting.setup.review')->with('warnings', $result['warnings']);
    }

    public function review(ReportService $reports)
    {
        $businessId = auth()->user()->business->id;
        $batchId = session('accounting.backfill_batch');
        $draft = JournalEntry::where('business_id', $businessId)->where('batch_id', $batchId)->draft()->exists();
        if (!$batchId || !$draft) {
            return redirect()->route('accounting.setup');
        }

        $accounts = \App\Models\Account::where('business_id', $businessId)->where('is_group', false)->orderBy('code')->get();
        $raw = \App\Models\JournalLine::where('journal_lines.business_id', $businessId)
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_entries.batch_id', $batchId)
            ->groupBy('journal_lines.account_id')
            ->selectRaw('journal_lines.account_id, SUM(journal_lines.debit) dr, SUM(journal_lines.credit) cr')
            ->get()->keyBy('account_id');

        $balances = $accounts->mapWithKeys(fn ($a) => [$a->id => [
            'name' => $a->name, 'code' => $a->code,
            'balance' => $a->isDebitNormal() ? (($raw[$a->id]->dr ?? 0) - ($raw[$a->id]->cr ?? 0)) : (($raw[$a->id]->cr ?? 0) - ($raw[$a->id]->dr ?? 0)),
        ]])->filter(fn ($r) => abs($r['balance']) > 0.005);

        return view('accounting.review', [
            'balances' => $balances,
            'warnings' => session('warnings', []),
            'moneyAccounts' => app(Ledger::class)->moneyAccounts($businessId),
        ]);
    }

    public function commit(Request $request, BackfillService $backfill)
    {
        $businessId = auth()->user()->business->id;
        $batchId = session('accounting.backfill_batch');
        $validated = $request->validate([
            'opening' => 'nullable|array',
            'opening.*' => 'nullable|numeric|min:0',
        ]);

        $backfill->commit($businessId, $batchId, $validated['opening'] ?? []);
        $request->session()->forget('accounting.backfill_batch');

        return redirect()->route('accounting.index')->with('success', 'Your books are set up. Every sale and payment now keeps them up to date.');
    }

    public function discard(Request $request, BackfillService $backfill)
    {
        $businessId = auth()->user()->business->id;
        $backfill->discard($businessId, session('accounting.backfill_batch', ''));
        $request->session()->forget('accounting.backfill_batch');

        return redirect()->route('accounting.setup')->with('success', 'Draft discarded — nothing was recorded.');
    }

    /** Start fresh from a date with opening balances only. */
    public function open(Request $request, Ledger $ledger, \App\Services\Accounting\PostingService $posting)
    {
        $businessId = auth()->user()->business->id;
        $validated = $request->validate([
            'ledger_start_date' => 'required|date|before_or_equal:today',
            'cash' => 'nullable|numeric|min:0',
            'mpesa' => 'nullable|numeric|min:0',
            'bank' => 'nullable|numeric|min:0',
            'card' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($businessId, $validated, $ledger, $posting) {
            foreach (['cash', 'mpesa', 'bank', 'card'] as $key) {
                if (($amt = (float) ($validated[$key] ?? 0)) > 0) {
                    $posting->post($businessId, [
                        'date' => $validated['ledger_start_date'],
                        'description' => 'Opening balance: '.ucfirst($key),
                        'lines' => [
                            ['account' => $key, 'debit' => $amt],
                            ['account' => 'opening_balance', 'credit' => $amt],
                        ],
                    ]);
                }
            }
            // Untouched stock self-books as opening inventory on first movement; also value it now
            \App\Models\Product::where('business_id', $businessId)->where('stock_quantity', '>', 0)
                ->get()->each(fn ($p) => $ledger->postOpeningStock($p));

            // Outstanding customer and supplier debts
            foreach (\App\Models\CustomerDebt::where('business_id', $businessId)->where('balance', '>', 0)->get() as $d) {
                $ledger->postCustomerDebtOpening($d);
            }
            foreach (\App\Models\SupplierDebt::where('business_id', $businessId)->where('balance', '>', 0)->get() as $d) {
                $ledger->postSupplierDebtOpening($d);
            }
            foreach (\App\Models\SalaryAdvance::where('business_id', $businessId)->where('remaining_balance', '>', 0)->get() as $a) {
                $ledger->postAdvanceOpening($a);
            }

            AccountingSetting::for($businessId)->update([
                'setup_mode' => 'opening',
                'setup_completed_at' => now(),
                'ledger_start_date' => $validated['ledger_start_date'],
            ]);
        });

        return redirect()->route('accounting.index')->with('success', 'Books opened with your starting balances.');
    }
}
