<?php

namespace Tests\Feature\Accounting;

use App\Exceptions\Accounting\LedgerException;
use App\Models\Account;
use App\Models\AccountingSetting;
use App\Models\Cost;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Services\Accounting\ChartOfAccounts;
use App\Services\Accounting\PostingService;

class PostingServiceTest extends AccountingTestCase
{
    private PostingService $posting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->posting = app(PostingService::class);
    }

    private function cashSale(array $overrides = []): ?JournalEntry
    {
        return $this->posting->post($this->businessId, array_merge([
            'date' => '2026-10-01',
            'description' => 'Cash sale',
            'lines' => [
                ['account' => 'cash', 'debit' => 1160],
                ['account' => 'sales', 'credit' => 1000],
                ['account' => 'vat_output', 'credit' => 160],
            ],
        ], $overrides));
    }

    private function cost(float $amount = 500): Cost
    {
        return Cost::create(['business_id' => $this->businessId, 'type' => 'rent', 'amount' => $amount, 'date' => '2026-10-01']);
    }

    public function test_chart_of_accounts_is_created_once_per_business(): void
    {
        $chart = app(ChartOfAccounts::class);
        $chart->ensure($this->businessId);
        $chart->ensure($this->businessId);

        $this->assertSame(count(ChartOfAccounts::TEMPLATE), Account::where('business_id', $this->businessId)->count());
        $cash = Account::where('business_id', $this->businessId)->where('system_key', 'cash')->first();
        $this->assertSame('1000', $cash->parent->code);
        $this->assertSame(0, Account::where('business_id', 'biz-2')->count());
    }

    public function test_balanced_entry_updates_account_balances(): void
    {
        $entry = $this->cashSale();

        $this->assertSame('JE-000001', $entry->entry_number);
        $this->assertSame('1160.00', $entry->total);
        $this->assertSame(1160.0, $this->balance('cash'));
        $this->assertSame(1000.0, $this->balance('sales'));
        $this->assertSame(160.0, $this->balance('vat_output'));
        $this->assertSame('JE-000002', $this->cashSale()->entry_number);
    }

    public function test_unbalanced_entry_is_rejected(): void
    {
        $this->expectException(LedgerException::class);

        $this->posting->post($this->businessId, ['lines' => [
            ['account' => 'cash', 'debit' => 100],
            ['account' => 'sales', 'credit' => 90],
        ]]);
    }

    public function test_tiny_difference_goes_to_rounding_account(): void
    {
        $this->posting->post($this->businessId, ['lines' => [
            ['account' => 'cash', 'debit' => 100.02],
            ['account' => 'sales', 'credit' => 100],
        ]]);

        $this->assertSame(-0.02, $this->balance('rounding'));
        $this->assertSame(
            (float) JournalLine::sum('debit'),
            (float) JournalLine::sum('credit')
        );
    }

    public function test_zero_entry_records_nothing(): void
    {
        $this->assertNull($this->posting->post($this->businessId, ['lines' => [
            ['account' => 'cash', 'debit' => 0],
            ['account' => 'sales', 'credit' => 0],
        ]]));
        $this->assertSame(0, JournalEntry::count());
    }

    public function test_same_source_event_is_posted_only_once(): void
    {
        $cost = $this->cost();
        $lines = [['account' => 'expense_rent', 'debit' => 500], ['account' => 'cash', 'credit' => 500]];

        $first = $this->posting->post($this->businessId, ['source' => $cost, 'event' => 'cost', 'lines' => $lines]);
        $second = $this->posting->post($this->businessId, ['source' => $cost, 'event' => 'cost', 'lines' => $lines]);

        $this->assertTrue($first->is($second));
        $this->assertSame(-500.0, $this->balance('cash'));
    }

    public function test_reversal_nets_to_zero_and_allows_reposting(): void
    {
        $cost = $this->cost();
        $lines = fn ($amt) => [['account' => 'expense_rent', 'debit' => $amt], ['account' => 'cash', 'credit' => $amt]];

        $this->posting->post($this->businessId, ['source' => $cost, 'event' => 'cost', 'lines' => $lines(500)]);
        $this->posting->reverseSource($cost);
        $this->assertSame(0.0, $this->balance('cash'));

        // Edit: repost with the new amount, then reverse again.
        $this->posting->post($this->businessId, ['source' => $cost, 'event' => 'cost', 'lines' => $lines(700)]);
        $this->assertSame(-700.0, $this->balance('cash'));
        $reversals = $this->posting->reverseSource($cost);

        $this->assertCount(1, $reversals);
        $this->assertSame(0.0, $this->balance('cash'));
        $this->assertSame(0.0, $this->balance('expense_rent'));
        $this->assertSame(4, JournalEntry::count());
    }

    public function test_posted_entries_are_immutable(): void
    {
        $entry = $this->cashSale();

        try {
            $entry->update(['description' => 'changed']);
            $this->fail('Posted entry was edited');
        } catch (LedgerException) {
        }

        try {
            $entry->delete();
            $this->fail('Posted entry was deleted');
        } catch (LedgerException) {
        }

        $this->expectException(LedgerException::class);
        $entry->lines()->first()->update(['debit' => 1]);
    }

    public function test_draft_entries_can_be_discarded(): void
    {
        $entry = $this->cashSale(['status' => JournalEntry::DRAFT, 'batch_id' => '7d5b6c5e-0000-4000-8000-000000000001']);
        $this->assertSame(0.0, $this->balance('cash'), 'Drafts must not affect balances');

        $entry->delete();
        $this->assertSame(0, JournalEntry::count());
    }

    public function test_lock_date_blocks_posting_and_reversal_moves_to_today(): void
    {
        $entry = $this->cashSale(['date' => '2026-09-15']);
        AccountingSetting::for($this->businessId)->update(['lock_date' => '2026-09-30']);

        try {
            $this->cashSale(['date' => '2026-09-20']);
            $this->fail('Posted into a locked period');
        } catch (LedgerException) {
        }

        $reversal = $this->posting->reverse($entry);
        $this->assertSame(now()->toDateString(), $reversal->posting_date->toDateString());
        $this->assertSame(0.0, $this->balance('cash'));
    }

    public function test_cannot_post_to_another_business_or_group_account(): void
    {
        app(ChartOfAccounts::class)->ensure('biz-2');
        $foreign = Account::where('business_id', 'biz-2')->where('system_key', 'cash')->first();

        try {
            $this->posting->post($this->businessId, ['lines' => [
                ['account' => $foreign->id, 'debit' => 10],
                ['account' => 'sales', 'credit' => 10],
            ]]);
            $this->fail('Posted to a foreign account');
        } catch (LedgerException) {
        }

        $this->expectException(LedgerException::class);
        $this->posting->post($this->businessId, ['lines' => [
            ['account' => 'assets', 'debit' => 10],
            ['account' => 'sales', 'credit' => 10],
        ]]);
    }

    public function test_payment_methods_map_to_money_accounts_with_overrides(): void
    {
        $this->assertSame('mpesa', $this->posting->paymentAccount($this->businessId, 'airtel_money')->system_key);
        $this->assertSame('bank', $this->posting->paymentAccount($this->businessId, 'cheque')->system_key);
        $this->assertSame('cash', $this->posting->paymentAccount($this->businessId, 'unknown')->system_key);

        $bank = $this->posting->account($this->businessId, 'bank');
        AccountingSetting::for($this->businessId)->update(['payment_accounts' => ['card' => $bank->id]]);
        $this->assertTrue($bank->is($this->posting->paymentAccount($this->businessId, 'card')));
    }

    public function test_party_is_recorded_on_lines(): void
    {
        $this->posting->post($this->businessId, ['lines' => [
            ['account' => 'receivable', 'debit' => 300, 'party' => ['type' => 'customer', 'id' => 42]],
            ['account' => 'sales', 'credit' => 300],
        ]]);

        $line = JournalLine::where('party_type', 'customer')->first();
        $this->assertSame('42', $line->party_id);
        $this->assertSame('300.00', $line->debit);
    }
}
