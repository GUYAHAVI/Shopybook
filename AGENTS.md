# Shopybook – agent notes

## Stack
- Laravel 12 / PHP 8.2, MySQL. Single database; every tenant table is scoped by `business_id` (a **string**, `businesses.id` is a string PK).
- Deployed to cPanel shared hosting (no long-running workers assumed).

## Tests
- Run accounting tests: `php vendor/bin/phpunit tests/Feature/Accounting` (~5s, SQLite in-memory).
- The full migration history uses MySQL-only SQL, so it can't run on SQLite. `tests/Feature/Accounting/AccountingTestCase.php` builds stub tables plus the real accounting migrations instead. When accounting code needs a new column, add it there too.
- `tests/Feature/ExampleTest.php` (GET `/`) hangs; don't use it as a smoke test.
- Check new migrations against MySQL without applying them: `php artisan migrate --pretend`.

## Accounting module (double-entry general ledger)
- Code: `app/Services/Accounting/` (`PostingService`, `Ledger` posting rules, `StockValuation`, `ChartOfAccounts`). Models: `Account`, `JournalEntry`, `JournalLine`, `StockLedgerEntry`, `AccountingSetting`, `SupplierPayment`.
- Every money or stock event must call a `Ledger` method **inside the same DB transaction** as the business change.
- Call `StockValuation`/`Ledger::issueStockForSale`/`receiveStock` **before** changing `products.stock_quantity`.
- Posted journal entries are immutable. To correct one, use `Ledger::reverse()` / `repostCost()`; never update or delete it.
- Posting is idempotent per (source model, event).
- Reports live in `app/Services/Accounting/ReportService.php` + `app/Http/Controllers/Accounting/` (routes `accounting.*`). Every report reads posted `journal_lines` only.
- Report view pages: `resources/views/accounting/` with shared header `partials/head.blade.php`.

## Purchasing (procure-to-pay)
- `purchase_orders` + `purchase_order_items`; statuses: draft → sent → partially_received → received (cancelled side-state). Edit/delete draft only.
- Receiving a PO creates a `stock_receipt` per line (`purchase_order_id`/`purchase_order_item_id` links) and posts `Ledger::receiveStock` + creates a `SupplierDebt` when `payment_mode=credit`, all inside `DB::transaction` in `PurchaseOrderController::receive` (same pattern as `ProductsController::processReceive`).
- `supplier_debts` is the supplier-bill/AP record (posts `Dr expense|inventory / Cr payable`); `supplier_payments` settle it (`Dr payable / Cr money`). `purchase_order_id` links bills back to a PO.

## Sales (order-to-cash)
- `quotations` + `quotation_items`; statuses: draft → sent → accepted|declined → converted (cancelled side-state). Edit/delete draft only. Customer is either a linked `customers` row or snapshot fields (prospect — a `Customer` is auto-created on conversion).
- `QuotationController::convert` creates an `Order` in one `DB::transaction`: server-side `TaxCalculator` totals, `Ledger::issueStockForSale` per line **before** `stock_quantity` changes, `postSale` (always Dr receivable / Cr sales+VAT + COGS/inventory), `Payment` + `postPayment` (Dr money / Cr receivable) when paid/partial, `CustomerDebt` for the unpaid balance, `invoice_number` via `AccountingSetting::nextInvoiceNumber()` when not fully paid.
- `customer_debts` is the AR bill; `recordPayment` on the order settles it. AR aging lives in `ReportService::aging($biz,'receivable')`.
- Accounting-test caveat: `created_at`/`payment_date` are NOT mass-assignable — set them directly in tests, and suppress the `Product::created` observer when backdating opening stock (`postOpeningStock` dates the entry with `product->created_at` or the triggering movement's date).
