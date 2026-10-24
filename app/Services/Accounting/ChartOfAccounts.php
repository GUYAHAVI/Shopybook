<?php

namespace App\Services\Accounting;

use App\Models\Account;
use Illuminate\Support\Facades\DB;

/**
 * Default chart of accounts for a Kenyan small business, plus lookups that map
 * Shopybook concepts (payment methods, cost types) onto accounts.
 */
class ChartOfAccounts
{
    /** [code, name, type, sub_type, system_key, parent_code, is_group] */
    public const TEMPLATE = [
        ['1000', 'Assets', Account::ASSET, null, 'assets', null, true],
        ['1010', 'Cash on Hand', Account::ASSET, 'cash', 'cash', '1000', false],
        ['1020', 'M-Pesa / Mobile Money', Account::ASSET, 'cash', 'mpesa', '1000', false],
        ['1030', 'Bank', Account::ASSET, 'bank', 'bank', '1000', false],
        ['1040', 'Card / PayPal Clearing', Account::ASSET, 'bank', 'card', '1000', false],
        ['1100', 'Customers Owe You (Accounts Receivable)', Account::ASSET, 'receivable', 'receivable', '1000', false],
        ['1150', 'Staff Advances', Account::ASSET, 'receivable', 'staff_advances', '1000', false],
        ['1200', 'Stock (Inventory)', Account::ASSET, 'inventory', 'inventory', '1000', false],
        ['1300', 'VAT on Purchases (Input VAT)', Account::ASSET, 'tax', 'vat_input', '1000', false],
        ['1500', 'Equipment & Fixtures', Account::ASSET, 'fixed_asset', 'fixed_assets', '1000', false],

        ['2000', 'Liabilities', Account::LIABILITY, null, 'liabilities', null, true],
        ['2010', 'You Owe Suppliers (Accounts Payable)', Account::LIABILITY, 'payable', 'payable', '2000', false],
        ['2100', 'VAT Owed to KRA (Output VAT)', Account::LIABILITY, 'tax', 'vat_output', '2000', false],
        ['2150', 'Customer Store Credit', Account::LIABILITY, 'payable', 'customer_credit', '2000', false],
        ['2200', 'Salaries Payable', Account::LIABILITY, 'payable', 'salaries_payable', '2000', false],

        ['3000', 'Equity', Account::EQUITY, null, 'equity', null, true],
        ['3010', "Owner's Capital", Account::EQUITY, null, 'capital', '3000', false],
        ['3020', "Owner's Drawings", Account::EQUITY, 'contra', 'drawings', '3000', false],
        ['3030', 'Retained Earnings', Account::EQUITY, null, 'retained_earnings', '3000', false],
        ['3040', 'Opening Balance Equity', Account::EQUITY, null, 'opening_balance', '3000', false],

        ['4000', 'Income', Account::INCOME, null, 'income', null, true],
        ['4010', 'Sales - Goods', Account::INCOME, 'sales', 'sales', '4000', false],
        ['4020', 'Service Income', Account::INCOME, 'sales', 'service_income', '4000', false],
        ['4030', 'Rental Income', Account::INCOME, 'sales', 'rental_income', '4000', false],
        ['4040', 'Other Income', Account::INCOME, null, 'other_income', '4000', false],
        ['4090', 'Sales Returns & Allowances', Account::INCOME, 'contra', 'sales_returns', '4000', false],

        ['5000', 'Cost of Sales', Account::EXPENSE, null, 'cost_of_sales', null, true],
        ['5010', 'Cost of Goods Sold', Account::EXPENSE, 'cogs', 'cogs', '5000', false],
        ['5020', 'Stock Adjustments / Shrinkage', Account::EXPENSE, 'cogs', 'stock_adjustment', '5000', false],
        ['5030', 'Uncategorised Purchases', Account::EXPENSE, 'cogs', 'purchases_misc', '5000', false],

        ['6000', 'Operating Expenses', Account::EXPENSE, null, 'expenses', null, true],
        ['6010', 'Rent', Account::EXPENSE, null, 'expense_rent', '6000', false],
        ['6020', 'Electricity & Utilities', Account::EXPENSE, null, 'expense_utility', '6000', false],
        ['6030', 'Water', Account::EXPENSE, null, 'expense_water', '6000', false],
        ['6040', 'Salaries & Wages', Account::EXPENSE, null, 'expense_salary', '6000', false],
        ['6050', 'Commissions', Account::EXPENSE, null, 'expense_commission', '6000', false],
        ['6060', 'Renovation & Repairs', Account::EXPENSE, null, 'expense_renovation', '6000', false],
        ['6070', 'Marketing & Activities', Account::EXPENSE, null, 'expense_activity', '6000', false],
        ['6080', 'M-Pesa & Bank Charges', Account::EXPENSE, null, 'expense_charges', '6000', false],
        ['6090', 'General & Miscellaneous', Account::EXPENSE, null, 'expense_misc', '6000', false],
        ['6100', 'Other Expenses', Account::EXPENSE, null, 'expense_other', '6000', false],
        ['6200', 'Rounding Differences', Account::EXPENSE, null, 'rounding', '6000', false],
    ];

    /** Shopybook payment method => system account key. */
    public const PAYMENT_METHODS = [
        'cash' => 'cash',
        'mpesa' => 'mpesa',
        'mobile_money' => 'mpesa',
        'airtel_money' => 'mpesa',
        'equitel' => 'mpesa',
        'bank_transfer' => 'bank',
        'bank' => 'bank',
        'cheque' => 'bank',
        'card' => 'card',
        'paypal' => 'card',
        'paystack' => 'card',
    ];

    /** Cost.type => system account key. */
    public const COST_TYPES = [
        'rent' => 'expense_rent',
        'utility' => 'expense_utility',
        'water' => 'expense_water',
        'salary' => 'expense_salary',
        'renovation' => 'expense_renovation',
        'activity' => 'expense_activity',
        'misc' => 'expense_misc',
        'other' => 'expense_other',
        'product' => 'purchases_misc',
    ];

    /** Creates any missing default accounts for the business. Safe to call repeatedly. */
    public function ensure(string $businessId): void
    {
        $existing = Account::where('business_id', $businessId)->get(['id', 'code', 'system_key']);
        $keys = $existing->pluck('system_key')->filter()->flip();
        $missing = array_filter(self::TEMPLATE, fn ($row) => !isset($keys[$row[4]]));
        if (!$missing) {
            return;
        }

        DB::transaction(function () use ($businessId, $existing, $missing) {
            $ids = $existing->pluck('id', 'code')->all();
            foreach ($missing as [$code, $name, $type, $subType, $key, $parent, $group]) {
                if (isset($ids[$code])) {
                    continue; // code taken by a user-defined account
                }
                $ids[$code] = Account::create([
                    'business_id' => $businessId,
                    'parent_id' => $parent ? ($ids[$parent] ?? null) : null,
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                    'sub_type' => $subType,
                    'system_key' => $key,
                    'is_group' => $group,
                ])->id;
            }
        });
    }

    public static function paymentMethodKey(?string $method): string
    {
        return self::PAYMENT_METHODS[strtolower((string) $method)] ?? 'cash';
    }

    public static function costTypeKey(?string $type): string
    {
        return self::COST_TYPES[strtolower((string) $type)] ?? 'expense_other';
    }
}
