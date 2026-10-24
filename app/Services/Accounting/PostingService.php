<?php

namespace App\Services\Accounting;

use App\Exceptions\Accounting\LedgerException;
use App\Models\Account;
use App\Models\AccountingSetting;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The only way entries get into the general ledger. Guarantees that every entry
 * balances, uses this business's accounts, respects the lock date and is never
 * posted twice for the same source event.
 */
class PostingService
{
    /** Differences up to this many cents are absorbed by the rounding account. */
    private const ROUNDING_TOLERANCE_CENTS = 5;

    private const PARTY_TYPES = [
        \App\Models\Customer::class => 'customer',
        \App\Models\Supplier::class => 'supplier',
        \App\Models\Staff::class => 'staff',
        \App\Models\Employee::class => 'employee',
        \App\Models\RentalTenant::class => 'tenant',
    ];

    /** @var array<string, array<string, Account>> business => system_key => account */
    private array $accounts = [];

    public function __construct(private ChartOfAccounts $chart)
    {
    }

    /**
     * Post a journal entry.
     *
     * $data keys:
     *  - lines: list of ['account' => system_key|id|Account, 'debit' => n, 'credit' => n, 'party' => Model|['type','id']|null, 'memo' => ?string]
     *  - date, description, source (Model), event (string), status (posted|draft), batch_id
     *
     * Returns null when every line is zero (nothing to record).
     */
    public function post(string $businessId, array $data): ?JournalEntry
    {
        $source = $data['source'] ?? null;
        $event = $data['event'] ?? 'post';
        $status = $data['status'] ?? JournalEntry::POSTED;
        $date = Carbon::parse($data['date'] ?? now())->toDateString();

        if ($source && empty($data['force_new']) && ($existing = $this->activeEntry($source, $event, $data['batch_id'] ?? null))) {
            return $existing;
        }

        $lines = $this->normaliseLines($businessId, $data['lines'] ?? []);
        if (!$lines) {
            return null;
        }
        $lines = $this->balance($businessId, $lines);

        $this->assertOpenPeriod($businessId, $date);

        return DB::transaction(function () use ($businessId, $data, $source, $event, $status, $date, $lines) {
            $entry = JournalEntry::create([
                'business_id' => $businessId,
                'entry_number' => $this->nextEntryNumber($businessId),
                'posting_date' => $date,
                'description' => isset($data['description']) ? mb_substr($data['description'], 0, 255) : null,
                'source_type' => $source ? $source->getMorphClass() : null,
                'source_id' => $source?->getKey(),
                'source_event' => $event,
                'reverses_id' => $data['reverses_id'] ?? null,
                'status' => $status,
                'batch_id' => $data['batch_id'] ?? null,
                'total' => array_sum(array_column($lines, 'debit')) / 100,
                'created_by' => auth()->id(),
            ]);

            JournalLine::insert(array_map(fn ($l) => [
                'journal_entry_id' => $entry->id,
                'business_id' => $businessId,
                'account_id' => $l['account_id'],
                'posting_date' => $date,
                'debit' => $l['debit'] / 100,
                'credit' => $l['credit'] / 100,
                'party_type' => $l['party_type'],
                'party_id' => $l['party_id'],
                'memo' => $l['memo'],
            ], $lines));

            return $entry;
        });
    }

    /**
     * Cancel an entry by posting its mirror image. Uses the original date unless that
     * period is locked, in which case the reversal lands today.
     */
    public function reverse(JournalEntry $entry, ?string $date = null, ?string $description = null): JournalEntry
    {
        if ($entry->reversed_by_id) {
            return $entry->reversedBy;
        }
        if ($entry->reverses_id) {
            throw new LedgerException('A reversal cannot itself be reversed; post a new entry instead.');
        }

        $date ??= $entry->posting_date->toDateString();
        if ($this->isLocked($entry->business_id, $date)) {
            $date = now()->toDateString();
        }

        return DB::transaction(function () use ($entry, $date, $description) {
            $lines = $entry->lines->map(fn (JournalLine $l) => [
                'account' => (int) $l->account_id,
                'debit' => (float) $l->credit,
                'credit' => (float) $l->debit,
                'party' => $l->party_type ? ['type' => $l->party_type, 'id' => $l->party_id] : null,
                'memo' => $l->memo,
            ])->all();

            $reversal = $this->post($entry->business_id, [
                'date' => $date,
                'description' => $description ?? 'Reversal of '.$entry->entry_number.($entry->description ? ': '.$entry->description : ''),
                'source' => $entry->source_type ? $this->sourceStub($entry) : null,
                'event' => 'reverse:'.$entry->source_event,
                'reverses_id' => $entry->id,
                'status' => $entry->status,
                'batch_id' => $entry->batch_id,
                'lines' => $lines,
                'force_new' => true,
            ]);

            $entry->update(['reversed_by_id' => $reversal->id]);

            return $reversal;
        });
    }

    /** Reverse every active entry for a source (optionally one event only). Returns the reversals. */
    public function reverseSource(Model $source, ?string $event = null, ?string $date = null): array
    {
        return JournalEntry::where('source_type', $source->getMorphClass())
            ->where('source_id', $source->getKey())
            ->when($event, fn ($q) => $q->where('source_event', $event))
            ->active()
            ->get()
            ->map(fn (JournalEntry $e) => $this->reverse($e, $date))
            ->all();
    }

    public function activeEntry(Model $source, string $event, ?string $batchId = null): ?JournalEntry
    {
        return JournalEntry::where('source_type', $source->getMorphClass())
            ->where('source_id', $source->getKey())
            ->where('source_event', $event)
            ->whereNull('reversed_by_id')
            ->where(fn ($q) => $q->where('status', JournalEntry::POSTED)->when($batchId, fn ($q) => $q->orWhere('batch_id', $batchId)))
            ->first();
    }

    /** Resolve a system key (e.g. 'mpesa'), id or Account into this business's Account. */
    public function account(string $businessId, string|int|Account $ref): Account
    {
        if ($ref instanceof Account) {
            $account = $ref;
        } elseif (is_int($ref) || ctype_digit($ref)) {
            $account = Account::find((int) $ref);
        } else {
            if (!isset($this->accounts[$businessId][$ref])) {
                $this->chart->ensure($businessId);
                $this->accounts[$businessId] = Account::where('business_id', $businessId)
                    ->whereNotNull('system_key')->get()->keyBy('system_key')->all();
            }
            $account = $this->accounts[$businessId][$ref] ?? null;
        }

        if (!$account || $account->business_id !== $businessId) {
            throw new LedgerException("Account [{$this->describe($ref)}] does not exist for this business.");
        }
        if ($account->is_group) {
            throw new LedgerException("Account [{$account->name}] is a group and cannot be posted to.");
        }

        return $account;
    }

    /** The money account a payment method lands in (cash, M-Pesa, bank...), honouring per-business overrides. */
    public function paymentAccount(string $businessId, ?string $method): Account
    {
        $override = AccountingSetting::for($businessId)->payment_accounts[strtolower((string) $method)] ?? null;

        return $this->account($businessId, $override ?: ChartOfAccounts::paymentMethodKey($method));
    }

    public function isLocked(string $businessId, string $date): bool
    {
        $lock = AccountingSetting::where('business_id', $businessId)->value('lock_date');

        return $lock && $date <= Carbon::parse($lock)->toDateString();
    }

    private function assertOpenPeriod(string $businessId, string $date): void
    {
        if ($this->isLocked($businessId, $date)) {
            throw new LedgerException("The books are closed for {$date}. Choose a later date or ask the owner to change the lock date.");
        }
    }

    private function normaliseLines(string $businessId, array $lines): array
    {
        $out = [];
        foreach ($lines as $line) {
            $debit = $this->cents($line['debit'] ?? 0);
            $credit = $this->cents($line['credit'] ?? 0);

            // A negative debit is a credit and vice versa.
            if ($debit < 0) { $credit -= $debit; $debit = 0; }
            if ($credit < 0) { $debit -= $credit; $credit = 0; }
            if ($debit > 0 && $credit > 0) {
                $net = $debit - $credit;
                [$debit, $credit] = $net >= 0 ? [$net, 0] : [0, -$net];
            }
            if ($debit === 0 && $credit === 0) {
                continue;
            }

            [$partyType, $partyId] = $this->party($line['party'] ?? null);

            $out[] = [
                'account_id' => $this->account($businessId, $line['account'])->id,
                'debit' => $debit,
                'credit' => $credit,
                'party_type' => $partyType,
                'party_id' => $partyId,
                'memo' => isset($line['memo']) ? mb_substr((string) $line['memo'], 0, 255) : null,
            ];
        }

        return $out;
    }

    private function balance(string $businessId, array $lines): array
    {
        $diff = array_sum(array_column($lines, 'debit')) - array_sum(array_column($lines, 'credit'));

        if ($diff !== 0) {
            if (abs($diff) > self::ROUNDING_TOLERANCE_CENTS) {
                throw new LedgerException(sprintf('Journal entry does not balance (difference %.2f).', $diff / 100));
            }
            $lines[] = [
                'account_id' => $this->account($businessId, 'rounding')->id,
                'debit' => $diff < 0 ? -$diff : 0,
                'credit' => $diff > 0 ? $diff : 0,
                'party_type' => null,
                'party_id' => null,
                'memo' => 'Rounding',
            ];
        }

        if (count($lines) < 2) {
            throw new LedgerException('A journal entry needs at least two lines.');
        }

        return $lines;
    }

    private function nextEntryNumber(string $businessId): string
    {
        AccountingSetting::for($businessId);
        $settings = AccountingSetting::where('business_id', $businessId)->lockForUpdate()->first();
        $number = $settings->next_entry_number;
        $settings->increment('next_entry_number');

        return 'JE-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    private function party(mixed $party): array
    {
        if ($party instanceof Model) {
            return [self::PARTY_TYPES[$party::class] ?? strtolower(class_basename($party)), (string) $party->getKey()];
        }
        if (is_array($party) && !empty($party['type'])) {
            return [$party['type'], isset($party['id']) ? (string) $party['id'] : null];
        }

        return [null, null];
    }

    private function cents(mixed $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    /** Lightweight model instance carrying only the source's type and key (for reversals). */
    private function sourceStub(JournalEntry $entry): Model
    {
        $class = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($entry->source_type) ?? $entry->source_type;
        $model = new $class;
        $model->{$model->getKeyName()} = $entry->source_id;

        return $model;
    }

    private function describe(mixed $ref): string
    {
        return $ref instanceof Account ? $ref->name : (string) $ref;
    }
}
