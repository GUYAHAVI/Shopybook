<?php

namespace App\Http\Controllers\Accounting;

use App\Exceptions\Accounting\LedgerException;
use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Services\Accounting\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Owner money movements: putting money in, taking it out, and moving it between
 * cash / M-Pesa / bank (e.g. banking cash or paying an M-Pesa withdrawal charge).
 * These keep the money account balances in line with what the owner actually has.
 */
class MoneyController extends Controller
{
    public function index(Ledger $ledger)
    {
        $business = auth()->user()->business;
        $accounts = $ledger->moneyAccounts($business->id);

        $balances = $accounts->mapWithKeys(fn ($a) => [$a->id => $a->balance()]);
        $accountIds = $accounts->pluck('id');

        $recent = JournalEntry::posted()
            ->where('business_id', $business->id)
            ->whereHas('lines', fn ($q) => $q->whereIn('account_id', $accountIds))
            ->whereNull('source_type') // business events (sales, costs) are shown elsewhere
            ->latest('posting_date')->latest('id')
            ->take(20)
            ->get();

        $settings = \App\Models\AccountingSetting::for($business->id);

        return view('accounting.money', compact('accounts', 'balances', 'recent', 'settings'));
    }

    /** Import a bank/M-Pesa statement CSV and auto-match lines against the ledger. */
    public function importStatement(Request $request, Ledger $ledger)
    {
        $business = auth()->user()->business;
        $data = $request->validate([
            'account_id' => ['required', 'integer', Ledger::moneyAccountRule($business->id)],
            'statement' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $rows = $this->parseStatementCsv($request->file('statement'));
        $imported = 0;
        foreach ($rows as $row) {
            $imported += (int) DB::table('statement_lines')->insertOrIgnore([
                'business_id' => $business->id,
                'account_id' => $data['account_id'],
                'txn_date' => $row['date'],
                'amount' => $row['amount'],
                'reference' => $row['ref'],
                'memo' => $row['memo'],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $matched = $this->matchStatement($business->id, $data['account_id']);

        return redirect()->route('money.index')->with('success', "Imported {$imported} lines, auto-matched {$matched}.");
    }

    /** M-Pesa export (Receipt No., Details, Completion Time, Paid In, Withdrawn) or generic date,amount,ref,memo CSV. */
    private function parseStatementCsv($file): array
    {
        $rows = [];
        $fh = fopen($file->getRealPath(), 'r');
        $header = array_map(fn ($h) => strtolower(trim($h)), fgetcsv($fh) ?: []);
        $col = fn (...$names) => count(array_intersect($names, $header)) ? array_search(reset(array_intersect($names, $header)), $header) : null;

        $iDate = $col('completion time', 'date', 'txn_date');
        $iRef = $col('receipt no.', 'reference', 'ref');
        $iIn = $col('paid in', 'paid_in', 'credit');
        $iOut = $col('withdrawn', 'debit');
        $iAmt = $col('amount');
        $iMemo = $col('details', 'memo', 'transaction details');

        while (($r = fgetcsv($fh)) !== false) {
            $date = $iDate !== null ? strtotime($r[$iDate] ?? '') : false;
            $amount = $iAmt !== null ? (float) preg_replace('/[^\d.\-]/', '', $r[$iAmt] ?? '0')
                : ((float) preg_replace('/[^\d.]/', '', $r[$iIn] ?? '0') - (float) preg_replace('/[^\d.]/', '', $r[$iOut] ?? '0'));
            if (!$date || $amount == 0.0) {
                continue;
            }
            $rows[] = [
                'date' => date('Y-m-d', $date),
                'amount' => $amount,
                'ref' => $iRef !== null ? trim($r[$iRef] ?? '') : null,
                'memo' => $iMemo !== null ? trim($r[$iMemo] ?? '') : null,
            ];
        }
        fclose($fh);

        return $rows;
    }

    /** Match unmatched statement lines to ledger lines on the account by amount and date (±3 days). */
    private function matchStatement(string $businessId, int $accountId): int
    {
        $matched = 0;
        $lines = \App\Models\JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_lines.business_id', $businessId)
            ->where('journal_lines.account_id', $accountId)
            ->where('journal_entries.status', JournalEntry::POSTED)
            ->get(['journal_lines.id', 'journal_lines.debit', 'journal_lines.credit', 'journal_lines.posting_date']);

        foreach (\App\Models\StatementLine::where('business_id', $businessId)->where('account_id', $accountId)->where('matched', false)->get() as $sl) {
            $hit = $lines->first(fn ($l) =>
                abs(($sl->amount > 0 ? $l->debit - $sl->amount : $l->credit + $sl->amount)) < 0.01
                && abs(\Carbon\Carbon::parse($l->posting_date)->diffInDays($sl->txn_date)) <= 3);
            if ($hit) {
                $sl->update(['matched' => true, 'journal_line_id' => $hit->id]);
                $matched++;
            }
        }

        return $matched;
    }

    /** Reconciliation view: unmatched statement lines and unmatched ledger lines. */
    public function reconcile(Request $request, Ledger $ledger)
    {
        $business = auth()->user()->business;
        $accountId = (int) $request->input('account_id');
        $accounts = $ledger->moneyAccounts($business->id);
        $account = $accountId ? $accounts->firstWhere('id', $accountId) : $accounts->first();

        $statements = \App\Models\StatementLine::where('business_id', $business->id)
            ->where('account_id', $account->id)->where('matched', false)->latest('txn_date')->get();

        return view('accounting.reconcile', compact('accounts', 'account', 'statements'));
    }

    /** Set the "books closed until" date — blocks posting into closed periods. */
    public function updateLock(Request $request)
    {
        $data = $request->validate(['lock_date' => 'nullable|date|before_or_equal:today']);
        \App\Models\AccountingSetting::for(auth()->user()->business->id)
            ->update(['lock_date' => $data['lock_date'] ?? null]);

        return back()->with('success', 'Books lock date updated.');
    }

    public function store(Request $request, Ledger $ledger)
    {
        $business = auth()->user()->business;
        $money = fn () => Ledger::moneyAccountRule($business->id);

        $data = $request->validate([
            'kind' => 'required|in:capital,drawing,transfer',
            'amount' => 'required|numeric|min:0.01',
            'from_account_id' => ['nullable', 'integer', $money()],
            'to_account_id' => ['nullable', 'integer', $money()],
            'charge' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'memo' => 'nullable|string|max:200',
        ]);

        $missing = match ($data['kind']) {
            'capital' => empty($data['to_account_id']) ? 'Which account did the money go into?' : null,
            'drawing' => empty($data['from_account_id']) ? 'Which account was the money taken from?' : null,
            'transfer' => empty($data['from_account_id']) || empty($data['to_account_id']) ? 'Choose both the "from" and "to" accounts.' : null,
        };
        if ($missing) {
            return back()->withInput()->withErrors(['kind' => $missing]);
        }

        try {
            DB::transaction(fn () => match ($data['kind']) {
                'capital' => $ledger->postOwnerInjection($business->id, $data['to_account_id'], (float) $data['amount'], $data['date'], $data['memo'] ?? null),
                'drawing' => $ledger->postOwnerDrawing($business->id, $data['from_account_id'], (float) $data['amount'], $data['date'], $data['memo'] ?? null),
                'transfer' => $ledger->postTransfer($business->id, $data['from_account_id'], $data['to_account_id'], (float) $data['amount'], (float) ($data['charge'] ?? 0), $data['date'], $data['memo'] ?? null),
            });
        } catch (LedgerException $e) {
            return back()->withInput()->withErrors(['kind' => $e->getMessage()]);
        }

        return redirect()->route('money.index')->with('success', 'Recorded.');
    }
}
