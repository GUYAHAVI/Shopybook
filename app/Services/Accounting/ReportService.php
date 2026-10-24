<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\StockLedgerEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * All financial reports are built from posted journal lines — the ledger is the
 * single source of truth. Reversed entries and their reversals cancel each other,
 * so they are simply included (status posted) rather than filtered out.
 */
class ReportService
{
    public function __construct(private Ledger $ledger)
    {
    }

    private function lines(string $businessId, ?string $from = null, ?string $to = null)
    {
        return JournalLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_lines.business_id', $businessId)
            ->where('journal_entries.status', JournalEntry::POSTED)
            ->when($from, fn ($q) => $q->where('journal_lines.posting_date', '>=', $from))
            ->when($to, fn ($q) => $q->where('journal_lines.posting_date', '<=', $to));
    }

    /** Per-account totals: [account_id => ['dr' => float, 'cr' => float]]. */
    private function totals(string $businessId, ?string $from, ?string $to): Collection
    {
        return $this->lines($businessId, $from, $to)
            ->groupBy('journal_lines.account_id')
            ->selectRaw('journal_lines.account_id, COALESCE(SUM(journal_lines.debit),0) as dr, COALESCE(SUM(journal_lines.credit),0) as cr')
            ->get()
            ->keyBy('account_id')
            ->map(fn ($r) => ['dr' => (float) $r->dr, 'cr' => (float) $r->cr]);
    }

    private function netBalance(Account $account, array $totals): float
    {
        $net = ($totals['dr'] ?? 0) - ($totals['cr'] ?? 0);

        return $account->isDebitNormal() ? $net : -$net;
    }

    /**
     * Trial balance: every account, its debits, credits and closing balance.
     */
    public function trialBalance(string $businessId, ?string $from = null, ?string $to = null): array
    {
        $accounts = Account::where('business_id', $businessId)
            ->where('is_group', false)->orderBy('code')->get();
        $totals = $this->totals($businessId, $from, $to);

        $rows = $accounts->map(function (Account $a) use ($totals) {
            $t = $totals[$a->id] ?? ['dr' => 0.0, 'cr' => 0.0];
            return [
                'account' => $a,
                'debit' => $t['dr'],
                'credit' => $t['cr'],
                'balance' => $this->netBalance($a, $t),
            ];
        });

        return [
            'rows' => $rows,
            'total_debits' => $rows->sum('debit'),
            'total_credits' => $rows->sum('credit'),
        ];
    }

    /**
     * Profit & loss for a period: revenue, less returns, cost of goods sold,
     * operating expenses, net profit. $previous is the same shape for comparison.
     */
    public function profitLoss(string $businessId, string $from, string $to): array
    {
        $totals = $this->totals($businessId, $from, $to);
        $accounts = Account::where('business_id', $businessId)
            ->where('is_group', false)->orderBy('code')->get()->keyBy('id');

        // separate collections — a shared collect() would alias them all to one bucket
        $revenue = collect(); $returns = collect(); $cogs = collect(); $expenses = collect();
        foreach ($accounts as $account) {
            $t = $totals[$account->id] ?? null;
            if (!$t) {
                continue;
            }
            $net = $this->netBalance($account, $t);
            if ($account->type === Account::INCOME) {
                ($account->sub_type === 'contra' ? $returns : $revenue)->push(['account' => $account, 'amount' => $net]);
            } elseif ($account->type === Account::EXPENSE) {
                ($account->sub_type === 'cogs' ? $cogs : $expenses)->push(['account' => $account, 'amount' => $net]);
            }
        }

        $grossRevenue = $revenue->sum('amount');
        $returnsTotal = $returns->sum('amount');
        $cogsTotal = $cogs->sum('amount');
        $expensesTotal = $expenses->sum('amount');
        $netSales = $grossRevenue - $returnsTotal;
        $grossProfit = $netSales - $cogsTotal;

        return [
            'revenue' => $revenue->values(),
            'returns' => $returns->values(),
            'cogs' => $cogs->values(),
            'expenses' => $expenses->values(),
            'gross_revenue' => $grossRevenue,
            'returns_total' => $returnsTotal,
            'net_sales' => $netSales,
            'cogs_total' => $cogsTotal,
            'gross_profit' => $grossProfit,
            'expenses_total' => $expensesTotal,
            'net_profit' => $grossProfit - $expensesTotal,
        ];
    }

    /**
     * Balance sheet at a date. Equity includes cumulative profit to date
     * (income minus expenses since the start of the books).
     */
    public function balanceSheet(string $businessId, string $asOf): array
    {
        $totals = $this->totals($businessId, null, $asOf);
        $accounts = Account::where('business_id', $businessId)
            ->where('is_group', false)->orderBy('code')->get();

        $assets = collect(); $liabilities = collect(); $equity = collect();
        $netProfit = 0.0;
        foreach ($accounts as $account) {
            $t = $totals[$account->id] ?? ['dr' => 0.0, 'cr' => 0.0];
            $net = $this->netBalance($account, $t);
            if (abs($net) < 0.005 && !$t['dr'] && !$t['cr']) {
                continue;
            }
            match ($account->type) {
                Account::ASSET => $assets->push(['account' => $account, 'balance' => $net]),
                Account::LIABILITY => $liabilities->push(['account' => $account, 'balance' => $net]),
                Account::EQUITY => $equity->push(['account' => $account, 'balance' => $net]),
                default => $netProfit += $account->type === Account::INCOME ? $net : -$net,
            };
        }

        $assetsTotal = $assets->sum('balance');
        $liabilitiesTotal = $liabilities->sum('balance');
        $equityTotal = $equity->sum('balance') + $netProfit;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'net_profit' => $netProfit,
            'assets_total' => $assetsTotal,
            'liabilities_total' => $liabilitiesTotal,
            'equity_total' => $equityTotal,
            'check' => round($assetsTotal - $liabilitiesTotal - $equityTotal, 2),
        ];
    }

    /** One account's lines with running balance — the "statement" behind every figure. */
    public function accountStatement(string $businessId, int $accountId, ?string $from = null, ?string $to = null): array
    {
        $account = Account::where('business_id', $businessId)->findOrFail($accountId);
        $opening = $this->netBalance($account, $this->totals($businessId, null, $from ? Carbon::parse($from)->subDay()->toDateString() : null)[$accountId] ?? []);

        $lines = $this->lines($businessId, $from, $to)
            ->where('journal_lines.account_id', $accountId)
            ->orderBy('journal_lines.posting_date')->orderBy('journal_lines.id')
            ->get(['journal_lines.*', 'journal_entries.entry_number', 'journal_entries.description', 'journal_entries.source_type', 'journal_entries.source_id', 'journal_entries.reverses_id', 'journal_entries.reversed_by_id']);

        $running = $opening;
        $lines->each(function ($line) use (&$running, $account) {
            $running = round($running + ($account->isDebitNormal() ? $line->debit - $line->credit : $line->credit - $line->debit), 2);
            $line->running_balance = $running;
        });

        return ['account' => $account, 'lines' => $lines, 'opening' => $opening, 'closing' => $running];
    }

    /** A customer/supplier's own ledger — every invoice, payment and credit note, with balance. */
    public function partyStatement(string $businessId, string $accountKey, string $partyType, int|string $partyId, ?string $from = null, ?string $to = null): array
    {
        $account = $this->ledger->account($businessId, $accountKey);
        $partyLines = fn () => $this->lines($businessId, $from, $to)
            ->where('journal_lines.account_id', $account->id)
            ->where('journal_lines.party_type', $partyType)
            ->where('journal_lines.party_id', $partyId);

        // opening: all movements before $from
        $openingQ = $this->lines($businessId, null, $from ? \Illuminate\Support\Carbon::parse($from)->subDay()->toDateString() : null)
            ->where('journal_lines.account_id', $account->id)
            ->where('journal_lines.party_type', $partyType)
            ->where('journal_lines.party_id', $partyId);
        $o = $openingQ->selectRaw('COALESCE(SUM(journal_lines.debit),0) dr, COALESCE(SUM(journal_lines.credit),0) cr')->first();
        $opening = $account->isDebitNormal() ? $o->dr - $o->cr : $o->cr - $o->dr;

        $lines = $partyLines()
            ->orderBy('journal_lines.posting_date')->orderBy('journal_lines.id')
            ->get(['journal_lines.*', 'journal_entries.entry_number', 'journal_entries.description', 'journal_entries.source_type', 'journal_entries.source_id', 'journal_entries.reverses_id', 'journal_entries.reversed_by_id']);

        $running = $opening;
        $lines->each(function ($line) use (&$running, $account) {
            $running = round($running + ($account->isDebitNormal() ? $line->debit - $line->credit : $line->credit - $line->debit), 2);
            $line->running_balance = $running;
        });

        return ['account' => $account, 'lines' => $lines, 'opening' => $opening, 'closing' => $running, 'partyType' => $partyType, 'partyId' => $partyId];
    }

    /** Money accounts: opening, money in, money out, closing for a period. */
    public function cashBook(string $businessId, string $from, string $to): array
    {
        $accounts = $this->ledger->moneyAccounts($businessId);
        $before = $this->totals($businessId, null, Carbon::parse($from)->subDay()->toDateString());
        $period = $this->totals($businessId, $from, $to);

        $rows = $accounts->map(function (Account $a) use ($before, $period) {
            $open = $this->netBalance($a, $before[$a->id] ?? []);
            $in = $period[$a->id]['dr'] ?? 0.0;
            $out = $period[$a->id]['cr'] ?? 0.0;
            return ['account' => $a, 'opening' => $open, 'in' => $in, 'out' => $out, 'closing' => round($open + $in - $out, 2)];
        });

        return ['rows' => $rows, 'in_total' => $rows->sum('in'), 'out_total' => $rows->sum('out')];
    }

    /**
     * Who owes you / who you owe, aged by how old the balance is.
     * $accountKey: 'receivable' or 'payable'.
     */
    public function aging(string $businessId, string $accountKey, string $asOf): Collection
    {
        $account = $this->ledger->account($businessId, $accountKey);
        $asOfDate = Carbon::parse($asOf);

        $lines = $this->lines($businessId, null, $asOf)
            ->where('journal_lines.account_id', $account->id)
            ->orderBy('journal_lines.posting_date')
            ->get(['journal_lines.party_type', 'journal_lines.party_id', 'journal_lines.memo', 'journal_lines.posting_date', 'journal_lines.debit', 'journal_lines.credit']);

        $parties = [];
        foreach ($lines as $line) {
            $key = ($line->party_type ?? 'other').':'.($line->party_id ?? 'unspecified');
            $amount = $account->isDebitNormal() ? $line->debit - $line->credit : $line->credit - $line->debit;
            if (abs($amount) < 0.005) {
                continue;
            }
            $days = $asOfDate->diffInDays(Carbon::parse($line->posting_date));
            $bucket = $days <= 30 ? 'current' : ($days <= 60 ? 'd30' : ($days <= 90 ? 'd60' : 'd90'));
            $parties[$key]['party'] = ['type' => $line->party_type, 'id' => $line->party_id, 'memo' => $line->memo];
            $parties[$key][$bucket] = ($parties[$key][$bucket] ?? 0) + $amount;
        }

        return collect($parties)->map(fn ($p) => [
            'party' => $p['party'],
            'current' => round($p['current'] ?? 0, 2),
            'd30' => round($p['d30'] ?? 0, 2),
            'd60' => round($p['d60'] ?? 0, 2),
            'd90' => round($p['d90'] ?? 0, 2),
            'total' => round(($p['current'] ?? 0) + ($p['d30'] ?? 0) + ($p['d60'] ?? 0) + ($p['d90'] ?? 0), 2),
        ])->filter(fn ($p) => abs($p['total']) > 0.005)->sortByDesc('total')->values();
    }

    /** Stock quantities and values from the stock ledger (must match the Inventory account). */
    public function stockValuation(string $businessId): Collection
    {
        $latest = StockLedgerEntry::where('business_id', $businessId)
            ->selectRaw('product_id, MAX(id) as last_id')->groupBy('product_id');

        return StockLedgerEntry::whereIn('id', $latest->pluck('last_id'))
            ->join('products', 'products.id', '=', 'stock_ledger_entries.product_id')
            ->orderBy('products.name')
            ->get(['stock_ledger_entries.product_id', 'products.name', 'products.sku', 'products.measurement_unit', 'stock_ledger_entries.qty_after', 'stock_ledger_entries.value_after', 'stock_ledger_entries.valuation_rate']);
    }

    /** VAT owed to KRA for a period: output VAT minus input VAT. */
    public function vatSummary(string $businessId, string $from, string $to): array
    {
        $totals = $this->totals($businessId, $from, $to);
        $out = $this->ledger->account($businessId, 'vat_output');
        $in = $this->ledger->account($businessId, 'vat_input');
        $output = $this->netBalance($out, $totals[$out->id] ?? []);
        $input = $this->netBalance($in, $totals[$in->id] ?? []);

        return ['output_vat' => $output, 'input_vat' => $input, 'payable' => round($output - $input, 2)];
    }
}
