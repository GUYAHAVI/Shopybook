<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\Cost;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockReceipt;
use App\Models\SupplierDebt;
use App\Models\SupplierPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Posting rules: translates Shopybook business events into journal entries.
 * Call inside the same DB transaction as the business action so both succeed or fail together.
 */
class Ledger
{
    public function __construct(private PostingService $posting, private StockValuation $stock)
    {
    }

    /**
     * Take sold stock out of inventory at moving-average cost. Call BEFORE reducing
     * products.stock_quantity. Returns [unit_cost, cost_amount] to store on the order item.
     */
    public function issueStockForSale(Product $product, float $stockQty, Order $order): array
    {
        [$rate, $value] = $this->stock->issue($product, $stockQty, $order, $order->created_at);

        return [round($rate, 4), $value];
    }

    /**
     * Sale: Dr Receivable (total) / Cr Sales (net) / Cr VAT Output (tax),
     * and Dr Cost of Goods Sold / Cr Inventory at cost.
     * $costOverride is used by orders that sell stock without order items (public orders).
     */
    public function postSale(Order $order, ?float $costOverride = null): ?JournalEntry
    {
        $order->loadMissing('items');
        $total = (float) $order->total_amount;
        $tax = (float) $order->tax;
        $cost = $costOverride ?? round($order->items->sum(fn ($i) => (float) $i->cost_amount), 2);
        $customer = $order->customer_id ? ['type' => 'customer', 'id' => $order->customer_id] : null;

        return $this->posting->post($order->business_id, [
            'date' => $order->created_at,
            'description' => "Sale {$order->order_number}",
            'source' => $order,
            'event' => 'sale',
            'lines' => [
                ['account' => 'receivable', 'debit' => $total, 'party' => $customer],
                ['account' => 'sales', 'credit' => round($total - $tax, 2)],
                ['account' => 'vat_output', 'credit' => $tax],
                ['account' => 'cogs', 'debit' => $cost],
                ['account' => 'inventory', 'credit' => $cost],
            ],
        ]);
    }

    /**
     * Cancel a completed sale: reverse the sale entry, put the stock back (also in
     * products.stock_quantity) and reverse the payments that were posted for it.
     * Call inside a transaction, before the order's status is changed.
     */
    public function cancelSale(Order $order): void
    {
        $this->posting->reverseSource($order, 'sale');
        $this->restockCancelledOrder($order);

        foreach ($order->payments as $payment) {
            $this->posting->reverseSource($payment);
        }
    }

    /** Put the stock a completed order removed back into inventory, at the original cost. */
    private function restockCancelledOrder(Order $order): void
    {
        // Reuse the stock ledger entries the sale wrote (works for item and public orders).
        $issues = \App\Models\StockLedgerEntry::where('business_id', $order->business_id)
            ->where('source_type', $order->getMorphClass())
            ->where('source_id', $order->id)
            ->where('qty_change', '<', 0)
            ->get();

        foreach ($issues as $issue) {
            $product = Product::find($issue->product_id);
            if (!$product) {
                continue;
            }
            $qty = -(float) $issue->qty_change;
            $rate = $qty > 0 ? -(float) $issue->value_change / $qty : 0;
            $this->stock->receive($product, $qty, $rate, $order);
            $product->increment('stock_quantity', $qty);
        }

        // Orders sold before the ledger existed have no stock entries: restock from order data.
        if ($issues->isEmpty()) {
            $order->loadMissing('items.product');
            if ($order->items->isEmpty() && $order->product_id && $order->quantity) {
                $product = $order->product ?: Product::find($order->product_id);
                if ($product) {
                    $this->stock->receive($product, (float) $order->quantity, $this->rateOf($product), $order);
                    $product->increment('stock_quantity', (float) $order->quantity);
                }
            }
            foreach ($order->items as $item) {
                $product = $item->product ?: Product::find($item->product_id);
                if (!$product) {
                    continue;
                }
                $qty = $item->stockQuantity();
                $rate = (float) ($item->unit_cost ?: $this->rateOf($product));
                $this->stock->receive($product, $qty, $rate, $order);
                $product->increment('stock_quantity', $qty);
            }
        }
    }

    private function rateOf(Product $product): float
    {
        [, , $rate] = $this->stock->position($product);
        return $rate;
    }

    /**
     * Return completed: Dr Sales Returns + Dr VAT Output (tax share) / Cr refund destination,
     * restocked goods at their sold cost (Dr Inventory / Cr COGS), restocking fee as income.
     * Call AFTER restockReturnGoods() so the inventory value is known.
     */
    public function postReturn(\App\Models\OrderReturn $return, float $restockValue): ?JournalEntry
    {
        $order = $return->order;
        $goodsValue = round((float) $return->refund_amount + (float) $return->restocking_fee, 2);
        $taxRatio = $order && (float) $order->total_amount > 0 ? (float) $order->tax / (float) $order->total_amount : 0.0;
        $vat = round($goodsValue * $taxRatio, 2);
        $customer = $return->customer_id ? ['type' => 'customer', 'id' => $return->customer_id] : null;
        $businessId = (string) $return->business_id;

        // Where the refund goes: still unpaid reduces the debt, store credit becomes a balance
        // the customer can spend later, everything else leaves a money account.
        if ($return->refund_method === 'store_credit') {
            $credit = ['account' => 'customer_credit', 'credit' => $return->refund_amount, 'party' => $customer];
        } elseif ($order && (float) ($order->balance_due ?? 0) > 0) {
            $credit = ['account' => 'receivable', 'credit' => $return->refund_amount, 'party' => $customer];
        } else {
            $credit = ['account' => $this->posting->paymentAccount($businessId, $return->refund_method), 'credit' => $return->refund_amount];
        }

        return $this->posting->post($businessId, [
            'date' => $return->refund_processed_at ?? now(),
            'description' => "Return {$return->return_number}".($order ? " on {$order->order_number}" : ''),
            'source' => $return,
            'event' => 'return',
            'lines' => [
                ['account' => 'sales_returns', 'debit' => round($goodsValue - $vat, 2), 'party' => $customer],
                ['account' => 'vat_output', 'debit' => $vat],
                $credit,
                ['account' => 'other_income', 'credit' => $return->restocking_fee, 'memo' => 'Restocking fee'],
                ['account' => 'inventory', 'debit' => $restockValue],
                ['account' => 'cogs', 'credit' => $restockValue],
            ],
        ]);
    }

    /**
     * Restock a completed return and return the total value restocked.
     * Also increments products.stock_quantity — callers must not increment again.
     */
    public function restockReturnGoods(\App\Models\OrderReturn $return): float
    {
        $value = 0.0;
        $order = $return->order;
        if (!$order) {
            return $value;
        }

        if ($return->return_type === 'full') {
            foreach ($order->items as $item) {
                $value += $this->restockItem($return, $item, $item->stockQuantity());
            }
        } elseif ($return->items_data) {
            $orderItems = $order->items;
            foreach ($return->items_data as $data) {
                $item = $orderItems->firstWhere('product_id', $data['product_id']);
                $qty = $item ? $item->stockQuantityFor((float) $data['quantity']) : (float) $data['quantity'];
                $value += $this->restockItem($return, $item, $qty, (int) $data['product_id']);
            }
        }

        return $value;
    }

    private function restockItem(\App\Models\OrderReturn $return, ?\App\Models\OrderItem $item, float $qty, ?int $productId = null): float
    {
        if ($qty <= 0) {
            return 0.0;
        }
        $product = ($item?->product) ?: Product::find($productId ?? $item?->product_id);
        if (!$product) {
            return 0.0;
        }
        $rate = (float) ($item?->unit_cost ?: $this->rateOf($product));
        $value = $this->stock->receive($product, $qty, $rate, $return, $return->refund_processed_at);
        $product->increment('stock_quantity', $qty);

        return $value;
    }

    /**
     * Approved credit note: reduces what the customer owes (Dr Sales Returns + Dr VAT Output /
     * Cr Receivable). It does not move money — a refund is posted by the return flow instead.
     */
    public function postCreditNote(\App\Models\CreditNote $creditNote): ?JournalEntry
    {
        $order = $creditNote->order;
        $businessId = (string) ($order?->business_id);
        $amount = (float) $creditNote->amount;
        $taxRatio = $order && (float) $order->total_amount > 0 ? (float) $order->tax / (float) $order->total_amount : 0.0;
        $vat = round($amount * $taxRatio, 2);
        $customer = $order?->customer_id ? ['type' => 'customer', 'id' => $order->customer_id] : null;

        return $this->posting->post($businessId, [
            'date' => $creditNote->approved_at ?? now(),
            'description' => "Credit note {$creditNote->credit_note_number}",
            'source' => $creditNote,
            'event' => 'credit_note',
            'lines' => [
                ['account' => 'sales_returns', 'debit' => round($amount - $vat, 2), 'party' => $customer, 'memo' => $creditNote->reason],
                ['account' => 'vat_output', 'debit' => $vat],
                ['account' => 'receivable', 'credit' => $amount, 'party' => $customer],
            ],
        ]);
    }

    /** Paid service: Dr money account / Cr Service Income. */
    public function postServiceBooking(\App\Models\ServiceBooking $booking): ?JournalEntry
    {
        if ($booking->payment_status !== 'paid' || (float) $booking->final_amount <= 0) {
            return null;
        }
        $businessId = (string) $booking->business_id;

        return $this->posting->post($businessId, [
            'date' => $booking->service_date ?? $booking->created_at,
            'description' => "Service sale #{$booking->id}",
            'source' => $booking,
            'event' => 'service',
            'lines' => [
                ['account' => $this->posting->paymentAccount($businessId, $booking->payment_method), 'debit' => $booking->final_amount, 'party' => ['type' => 'customer', 'id' => $booking->customer_id]],
                ['account' => 'service_income', 'credit' => $booking->final_amount],
            ],
        ]);
    }

    /** Rent received: Dr money account / Cr Rental Income. Posts only when status is paid. */
    public function postRentalPayment(\App\Models\RentalPayment $payment): ?JournalEntry
    {
        if ($payment->status !== 'paid' || (float) $payment->amount <= 0) {
            return null;
        }
        $businessId = (string) $payment->business_id;

        return $this->posting->post($businessId, [
            'date' => $payment->paid_at ?? now(),
            'description' => "Rent {$payment->period} — {$payment->tenant?->name}",
            'source' => $payment,
            'event' => 'rent',
            'lines' => [
                ['account' => $this->posting->paymentAccount($businessId, $payment->method), 'debit' => $payment->amount, 'memo' => $payment->transaction_reference],
                ['account' => 'rental_income', 'credit' => $payment->amount, 'party' => ['type' => 'tenant', 'id' => $payment->rental_tenant_id], 'memo' => $payment->period],
            ],
        ]);
    }

    /** Salary advance handed out: Dr Staff Advances / Cr money account (cash by default). */
    public function postSalaryAdvance(\App\Models\SalaryAdvance $advance, string|int|null $paidFrom = null): ?JournalEntry
    {
        $businessId = (string) $advance->business_id;

        return $this->posting->post($businessId, [
            'date' => $advance->paid_at ?? $advance->advance_date ?? now(),
            'description' => "Salary advance to {$advance->staff?->name}",
            'source' => $advance,
            'event' => 'advance',
            'lines' => [
                ['account' => 'staff_advances', 'debit' => $advance->amount, 'party' => ['type' => 'staff', 'id' => $advance->staff_id]],
                ['account' => $paidFrom ?: 'cash', 'credit' => $advance->amount],
            ],
        ]);
    }

    /** Part of an advance recovered from wages: Dr Salaries Payable / Cr Staff Advances. */
    public function postAdvanceDeduction(\App\Models\SalaryAdvance $advance, float $amount): ?JournalEntry
    {
        return $this->posting->post((string) $advance->business_id, [
            'description' => "Advance deduction — {$advance->staff?->name}",
            'source' => $advance,
            'event' => 'deduction',
            'force_new' => true, // one advance can be deducted in several instalments
            'lines' => [
                ['account' => 'salaries_payable', 'debit' => $amount, 'party' => ['type' => 'staff', 'id' => $advance->staff_id]],
                ['account' => 'staff_advances', 'credit' => $amount, 'party' => ['type' => 'staff', 'id' => $advance->staff_id]],
            ],
        ]);
    }

    /** Commission paid to staff: Dr Commissions / Cr money account. */
    public function postCommission(\App\Models\CommissionPayout $payout): ?JournalEntry
    {
        $businessId = (string) $payout->business_id;

        return $this->posting->post($businessId, [
            'date' => $payout->paid_at ?? $payout->created_at,
            'description' => "Commission payout — {$payout->staff?->name}",
            'source' => $payout,
            'event' => 'commission',
            'lines' => [
                ['account' => 'expense_commission', 'debit' => $payout->amount, 'party' => ['type' => 'staff', 'id' => $payout->staff_id]],
                ['account' => $payout->paid_from_account_id ?: 'cash', 'credit' => $payout->amount],
            ],
        ]);
    }

    /**
     * Manual stock change (stock edit, import, offline sync): records the quantity
     * difference at moving-average cost vs the Stock Adjustments account.
     * Call BEFORE changing products.stock_quantity — the previous quantity is taken
     * from the stock ledger (or the current level is booked as opening stock first).
     */
    public function postStockAdjustment(Product $product, float $newQty, ?Model $source = null): ?JournalEntry
    {
        [$oldQty, , $rate] = $this->stock->position($product);
        $delta = round($newQty - $oldQty, 3);
        if (abs($delta) < 0.0005) {
            return null;
        }
        $businessId = (string) $product->business_id;

        if ($delta > 0) {
            $value = $this->stock->receive($product, $delta, $rate, $source ?? $product);
            $lines = [
                ['account' => 'inventory', 'debit' => $value],
                ['account' => 'stock_adjustment', 'credit' => $value],
            ];
        } else {
            [, $value] = $this->stock->issue($product, -$delta, $source ?? $product);
            $lines = [
                ['account' => 'stock_adjustment', 'debit' => $value],
                ['account' => 'inventory', 'credit' => $value],
            ];
        }

        return $this->posting->post($businessId, [
            'description' => "Stock adjustment: {$product->name} ({$oldQty} → {$newQty})",
            'source' => $source ?? $product,
            'event' => 'stock_adjust',
            'force_new' => true, // each adjustment is a separate event
            'lines' => $lines,
        ]);
    }

    /** Customer payment: Dr Cash/M-Pesa/Bank / Cr Receivable. Gateway payments post only once confirmed. */
    public function postPayment(Payment $payment): ?JournalEntry
    {
        $pendingGateway = $payment->status === 'pending' && $payment->transaction_id;
        if ($pendingGateway || in_array($payment->status, ['failed', 'cancelled'], true)) {
            return null;
        }

        $order = $payment->order;
        $businessId = (string) ($payment->business_id ?: $order->business_id);
        $customer = $order?->customer_id ? ['type' => 'customer', 'id' => $order->customer_id] : null;

        return $this->posting->post($businessId, [
            'date' => $payment->payment_date ?? $payment->created_at,
            'description' => "Payment {$payment->payment_number}".($order ? " for {$order->order_number}" : ''),
            'source' => $payment,
            'event' => 'payment',
            'lines' => [
                ['account' => $this->posting->paymentAccount($businessId, $payment->payment_method), 'debit' => $payment->amount, 'memo' => $payment->transaction_reference],
                ['account' => 'receivable', 'credit' => $payment->amount, 'party' => $customer],
            ],
        ]);
    }

    /**
     * Stock received: Dr Inventory / Cr money account (paid now) or Cr Payable (on credit).
     * Call BEFORE increasing products.stock_quantity.
     */
    public function receiveStock(StockReceipt $receipt, Product $product): ?JournalEntry
    {
        $value = $this->stock->receive($product, (float) $receipt->quantity_received, (float) $receipt->unit_cost, $receipt, $receipt->receipt_date);

        $credit = $receipt->payment_mode === 'credit'
            ? ['account' => 'payable', 'credit' => $value, 'party' => ['type' => 'supplier', 'id' => $receipt->supplier_id], 'memo' => $receipt->supplier]
            : ['account' => $receipt->paid_from_account_id ?: 'cash', 'credit' => $value, 'memo' => $receipt->supplier];

        return $this->posting->post($receipt->business_id, [
            'date' => $receipt->receipt_date,
            'description' => "Stock received {$receipt->receipt_number}: {$receipt->product_name}",
            'source' => $receipt,
            'event' => 'receipt',
            'lines' => [
                ['account' => 'inventory', 'debit' => $value],
                $credit,
            ],
        ]);
    }

    /**
     * Manually recorded supplier bill: Dr chosen expense / Cr Payable.
     * Debts created by an on-credit stock receipt are already in the ledger via the receipt.
     */
    public function postSupplierDebt(SupplierDebt $debt): ?JournalEntry
    {
        if ($debt->stock_receipt_id) {
            return null;
        }

        return $this->posting->post((string) $debt->business_id, [
            'date' => $debt->created_at,
            'description' => "Supplier bill {$debt->reference_number}: {$debt->supplier_name}",
            'source' => $debt,
            'event' => 'bill',
            'lines' => [
                ['account' => $debt->expense_account_id ?: 'purchases_misc', 'debit' => $debt->total_amount, 'memo' => $debt->description],
                ['account' => 'payable', 'credit' => $debt->total_amount, 'party' => ['type' => 'supplier', 'id' => $debt->supplier_id], 'memo' => $debt->supplier_name],
            ],
        ]);
    }

    /** Paying a supplier: Dr Payable / Cr Cash/M-Pesa/Bank. */
    public function postSupplierPayment(SupplierPayment $payment): ?JournalEntry
    {
        $debt = $payment->debt;
        $businessId = (string) $payment->business_id;

        return $this->posting->post($businessId, [
            'date' => $payment->paid_at,
            'description' => "Payment to {$debt->supplier_name} ({$debt->reference_number})",
            'source' => $payment,
            'event' => 'payment',
            'lines' => [
                ['account' => 'payable', 'debit' => $payment->amount, 'party' => ['type' => 'supplier', 'id' => $debt->supplier_id], 'memo' => $debt->supplier_name],
                ['account' => $this->posting->paymentAccount($businessId, $payment->payment_method), 'credit' => $payment->amount, 'memo' => $payment->transaction_reference],
            ],
        ]);
    }

    /** Business expense: Dr expense (by cost type) / Cr money account it was paid from. */
    public function postCost(Cost $cost): ?JournalEntry
    {
        return $this->posting->post((string) $cost->business_id, [
            'date' => $cost->date,
            'description' => ucfirst($cost->type).' expense'.($cost->description ? ": {$cost->description}" : ''),
            'source' => $cost,
            'event' => 'cost',
            'lines' => [
                ['account' => ChartOfAccounts::costTypeKey($cost->type), 'debit' => $cost->amount],
                ['account' => $cost->paid_from_account_id ?: 'cash', 'credit' => $cost->amount],
            ],
        ]);
    }

    /** After editing a cost: cancel the old entry and post the new figures. */
    public function repostCost(Cost $cost): ?JournalEntry
    {
        $this->posting->reverseSource($cost, 'cost');

        return $this->postCost($cost);
    }

    /** Cancel everything posted for a record (e.g. when it is deleted). */
    public function reverse(Model $source): array
    {
        return $this->posting->reverseSource($source);
    }

    /** Owner puts money into the business: Dr money account / Cr Owner's Capital. */
    public function postOwnerInjection(string $businessId, string|int|Account $into, float $amount, ?string $date = null, ?string $memo = null): ?JournalEntry
    {
        return $this->posting->post($businessId, [
            'date' => $date ?? now(),
            'description' => 'Owner added money'.($memo ? ": {$memo}" : ''),
            'lines' => [
                ['account' => $into, 'debit' => $amount],
                ['account' => 'capital', 'credit' => $amount],
            ],
        ]);
    }

    /** Owner takes money out: Dr Owner's Drawings / Cr money account. */
    public function postOwnerDrawing(string $businessId, string|int|Account $from, float $amount, ?string $date = null, ?string $memo = null): ?JournalEntry
    {
        return $this->posting->post($businessId, [
            'date' => $date ?? now(),
            'description' => 'Owner withdrew money'.($memo ? ": {$memo}" : ''),
            'lines' => [
                ['account' => 'drawings', 'debit' => $amount],
                ['account' => $from, 'credit' => $amount],
            ],
        ]);
    }

    /**
     * Move money between accounts (banking cash, M-Pesa withdrawal): Dr destination /
     * Cr source, plus an optional charge booked to M-Pesa & Bank Charges.
     */
    public function postTransfer(string $businessId, string|int|Account $from, string|int|Account $to, float $amount, float $charge = 0.0, ?string $date = null, ?string $memo = null): ?JournalEntry
    {
        $fromAccount = $this->posting->account($businessId, $from);
        $toAccount = $this->posting->account($businessId, $to);
        if ($fromAccount->is($toAccount)) {
            throw new \App\Exceptions\Accounting\LedgerException('The two accounts are the same.');
        }

        return $this->posting->post($businessId, [
            'date' => $date ?? now(),
            'description' => "Transfer {$fromAccount->name} → {$toAccount->name}".($memo ? ": {$memo}" : ''),
            'lines' => [
                ['account' => $toAccount, 'debit' => $amount],
                ['account' => 'expense_charges', 'debit' => $charge, 'memo' => 'Transfer charge'],
                ['account' => $fromAccount, 'credit' => round($amount + $charge, 2)],
            ],
        ]);
    }

    /**
     * Book a product's existing stock as opening inventory (Dr Inventory / Cr Opening
     * Balance Equity) when it's created with stock or first moves. No-op if the
     * product already has stock ledger history.
     */
    public function postOpeningStock(Product $product): void
    {
        $this->stock->position($product);
    }

    /** Resolve a system key (e.g. 'mpesa'), id or Account into this business's Account. */
    public function account(string $businessId, string|int|Account $ref): Account
    {
        return $this->posting->account($businessId, $ref);
    }

    /** Salaries paid for a monthly salary cost: Dr Salaries Payable / Cr paid-from account. */
    public function postSalariesPaid(Cost $cost, string|int|Account $paidFrom, $paidAt = null): JournalEntry
    {
        return $this->posting->post((string) $cost->business_id, [
            'date' => $paidAt ?? now(),
            'description' => 'Salaries paid: '.$cost->description,
            'source' => $cost,
            'event' => 'salary_paid',
            'lines' => [
                ['account' => 'salaries_payable', 'debit' => $cost->amount],
                ['account' => $paidFrom, 'credit' => $cost->amount],
            ],
        ]);
    }

    /** Opening balance for an outstanding customer debt: Dr Receivable / Cr Opening Balance Equity. */
    public function postCustomerDebtOpening(\App\Models\CustomerDebt $debt): ?JournalEntry
    {
        return $this->posting->post((string) $debt->business_id, [
            'date' => now(),
            'description' => "Opening: customer owes (debt #{$debt->id})",
            'source' => $debt,
            'event' => 'opening',
            'lines' => [
                ['account' => 'receivable', 'debit' => $debt->balance, 'party' => ['type' => 'customer', 'id' => $debt->customer_id]],
                ['account' => 'opening_balance', 'credit' => $debt->balance],
            ],
        ]);
    }

    /** Opening balance for an outstanding supplier debt: Dr Opening Balance Equity / Cr Payable. */
    public function postSupplierDebtOpening(\App\Models\SupplierDebt $debt): ?JournalEntry
    {
        return $this->posting->post((string) $debt->business_id, [
            'date' => now(),
            'description' => "Opening: owed to {$debt->supplier_name}",
            'source' => $debt,
            'event' => 'opening',
            'lines' => [
                ['account' => 'opening_balance', 'debit' => $debt->balance],
                ['account' => 'payable', 'credit' => $debt->balance, 'party' => ['type' => 'supplier', 'id' => $debt->supplier_id], 'memo' => $debt->supplier_name],
            ],
        ]);
    }

    /** Opening balance for an outstanding staff advance: Dr Staff Advances / Cr Opening Balance Equity. */
    public function postAdvanceOpening(\App\Models\SalaryAdvance $advance): ?JournalEntry
    {
        return $this->posting->post((string) $advance->business_id, [
            'date' => now(),
            'description' => "Opening: staff advance to {$advance->staff?->name}",
            'source' => $advance,
            'event' => 'opening',
            'lines' => [
                ['account' => 'staff_advances', 'debit' => $advance->remaining_balance, 'party' => ['type' => 'staff', 'id' => $advance->staff_id]],
                ['account' => 'opening_balance', 'credit' => $advance->remaining_balance],
            ],
        ]);
    }

    /** Accounts money can be paid from / into (cash, M-Pesa, bank...), for form dropdowns. */
    public function moneyAccounts(string $businessId): Collection
    {
        $this->posting->account($businessId, 'cash'); // makes sure the chart exists

        return Account::where('business_id', $businessId)
            ->whereIn('sub_type', ['cash', 'bank'])
            ->where('is_group', false)->where('is_active', true)
            ->orderBy('code')->get();
    }

    public function expenseAccounts(string $businessId): Collection
    {
        $this->posting->account($businessId, 'cash');

        return Account::where('business_id', $businessId)
            ->where('type', Account::EXPENSE)
            ->where('is_group', false)->where('is_active', true)
            ->whereNotIn('system_key', ['cogs', 'stock_adjustment', 'rounding'])
            ->orderBy('code')->get();
    }

    /** Validation rule: the id must be one of this business's money accounts. */
    public static function moneyAccountRule(string $businessId): \Illuminate\Validation\Rules\Exists
    {
        return Rule::exists('accounts', 'id')->where('business_id', $businessId)
            ->whereIn('sub_type', ['cash', 'bank'])->where('is_group', false);
    }

    public static function expenseAccountRule(string $businessId): \Illuminate\Validation\Rules\Exists
    {
        return Rule::exists('accounts', 'id')->where('business_id', $businessId)
            ->where('type', Account::EXPENSE)->where('is_group', false);
    }
}
