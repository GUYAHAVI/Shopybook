# Product Receiving Functionality

## Overview
A comprehensive product receiving system that allows businesses to properly track incoming stock for accounting purposes. The system supports receiving both existing products and new products, maintaining a complete audit trail of all stock receipts.

## Features

### 1. **Receive Stock Form**
- **Route**: `/products/receive`
- **Features**:
  - Toggle between existing and new products
  - Auto-generate unique receipt numbers (Format: `RCV[YYYYMMDD][0001]`)
  - Real-time stock calculations
  - Supplier and invoice tracking
  - Notes field for additional information

### 2. **Receipt History**
- **Route**: `/products/receive/history`
- **Features**:
  - Comprehensive list of all stock receipts
  - Filter by date range, receipt type, and search terms
  - Summary statistics (total units, total value, recent receipts)
  - Pagination for large datasets
  - Quick access to individual receipt details

### 3. **Individual Receipt View**
- **Route**: `/products/receive/{receipt}`
- **Features**:
  - Detailed receipt information
  - Product and supplier details
  - Cost breakdown
  - Print-friendly format
  - Link to product details

## Database Schema

### `stock_receipts` Table
```sql
- id (bigint, primary key)
- business_id (string, foreign key to businesses)
- product_id (bigint, nullable, foreign key to products)
- receipt_number (string, unique)
- product_name (string)
- supplier (string, nullable)
- quantity_received (integer)
- unit_cost (decimal 10,2, nullable)
- total_cost (decimal 10,2, nullable)
- receipt_date (date)
- invoice_number (string, nullable)
- notes (text, nullable)
- received_by (bigint, foreign key to users)
- receipt_type (enum: 'existing_product', 'new_product')
- additional_data (json, nullable)
- timestamps
```

## Models

### `StockReceipt` Model
Location: `app/Models/StockReceipt.php`

**Relationships**:
- `belongsTo(Business::class)`
- `belongsTo(Product::class)`
- `belongsTo(User::class, 'received_by')`

**Key Methods**:
- `generateReceiptNumber()`: Auto-generates unique receipt numbers
- `scopeExistingProducts()`: Filter for existing product receipts
- `scopeNewProducts()`: Filter for new product receipts
- `scopeDateRange()`: Filter by date range

**Attributes**:
- `formatted_total_cost`: Returns formatted total cost (KSh X,XXX.XX)
- `formatted_unit_cost`: Returns formatted unit cost (KSh X,XXX.XX)

### Updated `Product` Model
Location: `app/Models/Product.php`

**New Relationships**:
- `stockReceipts()`: Get all stock receipts for the product
- `latestStockReceipt()`: Get the most recent stock receipt

## Controllers

### ProductsController
Location: `app/Http/Controllers/ProductsController.php`

**New Methods**:

1. **`showReceiveForm()`**
   - Displays the stock receiving form
   - Loads all products for selection

2. **`processReceive(Request $request)`**
   - Handles stock receipt submission
   - Validates input data
   - Creates stock receipt record
   - Updates product stock quantity
   - Supports both existing and new products
   - Uses database transactions for data integrity

3. **`receiptHistory(Request $request)`**
   - Shows paginated list of all receipts
   - Supports filtering and searching
   - Displays summary statistics

4. **`showReceipt(StockReceipt $receipt)`**
   - Shows detailed view of a specific receipt
   - Print-friendly format

## Routes

```php
// Product Receiving Routes
Route::get('/products/receive', [ProductsController::class, 'showReceiveForm'])
    ->name('products.receive');
    
Route::post('/products/receive/process', [ProductsController::class, 'processReceive'])
    ->name('products.receive.process');
    
Route::get('/products/receive/history', [ProductsController::class, 'receiptHistory'])
    ->name('products.receive.history');
    
Route::get('/products/receive/{receipt}', [ProductsController::class, 'showReceipt'])
    ->name('products.receive.show');
```

## Views

### 1. Receive Form (`resources/views/business/products/receive.blade.php`)
- Dynamic form that switches between existing and new products
- JavaScript-powered real-time calculations
- Validation and error handling
- Responsive design

### 2. Receipt History (`resources/views/business/products/receipt-history.blade.php`)
- Summary statistics cards
- Advanced filtering options
- Searchable table
- Pagination
- Badge indicators for receipt types

### 3. Receipt Details (`resources/views/business/products/receipt-show.blade.php`)
- Professional receipt layout
- Complete receipt information
- Supplier and product details
- Print stylesheet included
- Cost breakdown table

## Usage Workflow

### Receiving Existing Products
1. Navigate to Products → Receive Stock
2. Select "Existing Product"
3. Choose the product from dropdown
4. Enter quantity received
5. Optionally update unit cost
6. Add supplier and invoice details
7. Submit to record receipt

### Receiving New Products
1. Navigate to Products → Receive Stock
2. Select "New Product"
3. Fill in product details (name, SKU, category, etc.)
4. Enter quantity and costs
5. Set selling price
6. Add supplier and invoice details
7. Submit to create product and record receipt

### Viewing History
1. Navigate to Products → Receipt History (or from the receive form)
2. Use filters to narrow down receipts
3. Search by receipt number, product name, or supplier
4. Click on any receipt to view details

## Integration Points

### Products Index Page
- Added "Receive Stock" button in the header
- Added "Receipt History" link in dropdown menu

### Inventory Page
- Added "Receive Stock" button for quick access

## Accounting Benefits

1. **Complete Audit Trail**: Every stock receipt is recorded with timestamp, user, and cost information
2. **Cost Tracking**: Track unit costs for each receipt, enabling FIFO/LIFO/Average cost calculations
3. **Supplier Management**: Link receipts to suppliers for vendor analysis
4. **Invoice Reconciliation**: Record invoice/PO numbers for easy matching
5. **Historical Data**: Full history of all stock receipts for reporting and analysis
6. **Stock Valuation**: Calculate inventory value based on actual receipt costs

## Best Practices

1. **Always record receipts** when stock arrives, not when ordered
2. **Include invoice numbers** for easy reconciliation with supplier invoices
3. **Update unit costs** if they differ from the last cost price
4. **Add notes** for unusual circumstances (damaged goods, partial delivery, etc.)
5. **Regular review** of receipt history to identify patterns and issues

## Future Enhancements

Potential improvements could include:
- Return/adjustment functionality
- Batch receiving for multiple products
- Email notifications for receipts
- Integration with purchase orders
- Cost averaging algorithms
- Supplier performance tracking
- Receipt approval workflow
- Mobile app for receiving
- Barcode scanning integration

## Migration

To set up the database table, run:
```bash
php artisan migrate
```

The migration file is located at:
`database/migrations/2025_10_04_000001_create_stock_receipts_table.php`

## Security

- All routes are protected with `has.business` middleware
- Receipt access is validated against the user's business
- Database transactions ensure data integrity
- Authorization checks prevent cross-business access

## Testing

To test the functionality:
1. Create or select a business
2. Navigate to Products → Receive Stock
3. Test both existing product and new product flows
4. Verify stock quantities are updated correctly
5. Check receipt history displays correctly
6. Print a receipt to test the print layout



