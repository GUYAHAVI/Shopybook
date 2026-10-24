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
