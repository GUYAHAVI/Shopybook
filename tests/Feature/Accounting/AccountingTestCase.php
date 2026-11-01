<?php

namespace Tests\Feature\Accounting;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The full migration history uses MySQL-only statements, so accounting tests build
 * a minimal schema on in-memory SQLite: stub business tables + the real accounting migration.
 */
abstract class AccountingTestCase extends TestCase
{
    protected string $businessId = 'biz-1';

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('businesses', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('name');
            $t->timestamps();
        });
        Schema::create('costs', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('type');
            $t->decimal('amount', 15, 2);
            $t->unsignedBigInteger('paid_from_account_id')->nullable();
            $t->text('description')->nullable();
            $t->date('date');
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('name');
            $t->string('slug')->nullable();
            $t->decimal('price', 15, 2)->default(0);
            $t->decimal('cost_price', 15, 2)->nullable();
            $t->decimal('valuation_rate', 15, 4)->nullable();
            $t->decimal('stock_quantity', 12, 3)->default(0);
            $t->timestamps();
        });
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('order_number')->nullable();
            $t->string('status')->default('completed');
            $t->string('order_type')->nullable();
            $t->decimal('subtotal', 15, 2)->default(0);
            $t->decimal('tax', 15, 2)->default(0);
            $t->decimal('tax_rate', 5, 2)->nullable();
            $t->boolean('tax_inclusive')->default(false);
            $t->string('tax_type')->nullable();
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->decimal('balance_due', 15, 2)->default(0);
            $t->decimal('total_price', 15, 2)->nullable();
            $t->string('payment_status')->nullable();
            $t->string('payment_method')->nullable();
            $t->decimal('amount_paid', 15, 2)->default(0);
            $t->string('invoice_number')->nullable();
            $t->timestamp('invoice_generated_at')->nullable();
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('sold_by')->nullable();
            $t->string('customer_name')->nullable();
            $t->string('customer_phone')->nullable();
            $t->string('customer_email')->nullable();
            $t->timestamps();
        });
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('order_id');
            $t->unsignedBigInteger('product_id');
            $t->decimal('quantity', 10, 3);
            $t->decimal('stock_qty', 12, 3)->nullable();
            $t->decimal('unit_cost', 15, 4)->nullable();
            $t->decimal('cost_amount', 15, 2)->nullable();
            $t->decimal('price', 15, 2);
            $t->decimal('total', 15, 2);
            $t->decimal('tax_amount', 15, 2)->nullable();
            $t->string('tax_treatment')->nullable();
            $t->string('unit')->nullable();
            $t->string('sell_unit')->nullable();
            $t->string('material_type')->nullable();
            $t->decimal('original_quantity', 10, 4)->nullable();
            $t->timestamps();
        });
        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('order_id')->nullable();
            $t->string('business_id')->nullable();
            $t->string('payment_number')->nullable();
            $t->string('payment_method');
            $t->decimal('amount', 15, 2);
            $t->string('transaction_id')->nullable();
            $t->string('transaction_reference')->nullable();
            $t->string('status')->nullable();
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('received_by')->nullable();
            $t->timestamp('payment_date')->nullable();
            $t->timestamps();
        });
        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->timestamps();
        });
        Schema::create('customer_debts', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('customer_id');
            $t->unsignedBigInteger('order_id')->nullable();
            $t->decimal('total_amount', 15, 2);
            $t->decimal('amount_paid', 15, 2)->default(0);
            $t->decimal('balance', 15, 2);
            $t->date('due_date')->nullable();
            $t->string('status')->default('pending');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('tax_settings', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->boolean('tax_enabled')->default(false);
            $t->string('tax_type')->nullable();
            $t->decimal('tax_rate', 5, 2)->default(0);
            $t->string('tax_name')->nullable();
            $t->boolean('tax_inclusive')->default(false);
            $t->json('tax_exemption_items')->nullable();
            $t->timestamps();
        });
        Schema::create('quotations', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('quote_number');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name')->nullable();
            $t->string('customer_email')->nullable();
            $t->string('customer_phone')->nullable();
            $t->string('status')->default('draft');
            $t->date('quote_date');
            $t->date('valid_until')->nullable();
            $t->decimal('subtotal', 15, 2)->default(0);
            $t->decimal('tax', 15, 2)->default(0);
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->unsignedBigInteger('order_id')->nullable();
            $t->timestamps();
        });
        Schema::create('quotation_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('quotation_id');
            $t->unsignedBigInteger('product_id');
            $t->string('product_name');
            $t->decimal('quantity', 12, 3);
            $t->decimal('unit_price', 15, 2);
            $t->decimal('total', 15, 2);
            $t->timestamps();
        });
        Schema::create('stock_receipts', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('product_id');
            $t->string('receipt_number')->nullable();
            $t->string('product_name');
            $t->string('supplier')->nullable();
            $t->unsignedBigInteger('supplier_id')->nullable();
            $t->unsignedBigInteger('purchase_order_id')->nullable();
            $t->unsignedBigInteger('purchase_order_item_id')->nullable();
            $t->decimal('quantity_received', 12, 3);
            $t->decimal('unit_cost', 15, 2);
            $t->decimal('total_cost', 15, 2);
            $t->string('payment_mode')->default('paid');
            $t->unsignedBigInteger('paid_from_account_id')->nullable();
            $t->date('receipt_date');
            $t->string('invoice_number')->nullable();
            $t->timestamps();
        });
        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('name');
            $t->timestamps();
        });
        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('po_number');
            $t->unsignedBigInteger('supplier_id');
            $t->string('status')->default('draft');
            $t->date('order_date');
            $t->date('expected_date')->nullable();
            $t->decimal('total_amount', 15, 2)->default(0);
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
        });
        Schema::create('purchase_order_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('purchase_order_id');
            $t->unsignedBigInteger('product_id');
            $t->string('product_name');
            $t->decimal('quantity_ordered', 12, 3);
            $t->decimal('quantity_received', 12, 3)->default(0);
            $t->decimal('unit_cost', 15, 2);
            $t->decimal('total_cost', 15, 2);
            $t->timestamps();
        });
        Schema::create('supplier_debts', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('supplier_id')->nullable();
            $t->unsignedBigInteger('stock_receipt_id')->nullable();
            $t->unsignedBigInteger('purchase_order_id')->nullable();
            $t->unsignedBigInteger('expense_account_id')->nullable();
            $t->string('supplier_name')->nullable();
            $t->string('reference_number')->nullable();
            $t->decimal('total_amount', 15, 2);
            $t->decimal('amount_paid', 15, 2)->default(0);
            $t->decimal('balance', 15, 2);
            $t->date('due_date')->nullable();
            $t->string('status')->default('pending');
            $t->text('description')->nullable();
            $t->timestamps();
        });

        Schema::create('returns', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('order_id');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('return_number')->nullable();
            $t->string('return_type');
            $t->string('status')->default('pending');
            $t->decimal('original_amount', 15, 2)->default(0);
            $t->decimal('refund_amount', 15, 2)->default(0);
            $t->decimal('restocking_fee', 15, 2)->default(0);
            $t->string('refund_method')->nullable();
            $t->boolean('return_to_stock')->default(true);
            $t->boolean('stock_returned')->default(false);
            $t->json('items_data')->nullable();
            $t->timestamp('refund_processed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('credit_notes', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('order_id');
            $t->string('credit_note_number')->nullable();
            $t->decimal('amount', 15, 2);
            $t->string('reason')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();
        });
        Schema::create('service_bookings', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->timestamp('service_date')->nullable();
            $t->decimal('final_amount', 15, 2)->default(0);
            $t->string('payment_status')->default('pending');
            $t->string('payment_method')->nullable();
            $t->timestamps();
        });
        Schema::create('rental_tenants', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('name');
            $t->timestamps();
        });
        Schema::create('rental_payments', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('rental_tenant_id')->nullable();
            $t->unsignedBigInteger('rental_unit_id')->nullable();
            $t->string('period');
            $t->decimal('amount', 15, 2);
            $t->string('method')->nullable();
            $t->string('status')->default('paid');
            $t->string('transaction_reference')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });
        Schema::create('staff', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->string('name');
            $t->decimal('salary', 15, 2)->default(0);
            $t->timestamps();
        });
        Schema::create('salary_advances', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('staff_id');
            $t->decimal('amount', 15, 2);
            $t->decimal('remaining_balance', 15, 2)->default(0);
            $t->date('advance_date')->nullable();
            $t->string('status')->default('pending');
            $t->string('deduction_status')->default('pending');
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });
        Schema::create('commission_payouts', function (Blueprint $t) {
            $t->id();
            $t->string('business_id');
            $t->unsignedBigInteger('staff_id');
            $t->decimal('amount', 15, 2);
            $t->unsignedBigInteger('paid_from_account_id')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
        });

        (require database_path('migrations/2026_10_24_000001_create_accounting_tables.php'))->up();
        (require database_path('migrations/2026_10_24_000003_create_supplier_payments_table.php'))->up();
        (require database_path('migrations/2026_10_24_000004_add_invoice_counter_to_accounting_settings.php'))->up();

        DB::table('businesses')->insert([
            ['id' => 'biz-1', 'name' => 'Duka One'],
            ['id' => 'biz-2', 'name' => 'Duka Two'],
        ]);
    }

    /** Balance of a system account in its natural direction. */
    protected function balance(string $key, ?string $businessId = null): float
    {
        return \App\Models\Account::where('business_id', $businessId ?? $this->businessId)
            ->where('system_key', $key)->firstOrFail()->balance();
    }

    protected function assertLedgerBalances(): void
    {
        $this->assertEqualsWithDelta(
            (float) \App\Models\JournalLine::sum('debit'),
            (float) \App\Models\JournalLine::sum('credit'),
            0.001,
            'Total debits must equal total credits'
        );
    }
}
