# Low Stock Notification System

## Overview
Automatic notification system that alerts business owners via email and dashboard notifications when product stock levels fall below defined thresholds.

## Features

### ✅ **Dual Notification Channels**
1. **Email Notifications**: Professional HTML emails sent to business email or owner email
2. **Dashboard Notifications**: Real-time notifications visible in the dashboard

### ✅ **Smart Notification Logic**
- Automatically checks stock after every sale/order
- Checks stock after manual stock updates
- Prevents duplicate notifications (24-hour cooldown)
- Distinguishes between "Low Stock" and "Out of Stock"

### ✅ **Beautiful Email Template**
- Color-coded alerts (Yellow for Low Stock, Red for Out of Stock)
- Complete product information
- Current stock levels prominently displayed
- Actionable recommendations
- Direct links to receive stock

## How It Works

### Automatic Triggers

#### 1. **After Order Processing** (POS Sales)
```php
// In SalesController.php
// Stock is reduced after each order
$product->stock_quantity -= $stockToDeduct;
$product->save();

// Automatic low stock check
$notificationService->checkAndNotifyLowStock($product);
```

#### 2. **After Manual Stock Updates**
```php
// In ProductsController.php
// When stock is manually updated
$product->stock_quantity = $request->stock_quantity;
$product->save();

// Automatic low stock check
$notificationService->checkAndNotifyLowStock($product);
```

#### 3. **Scheduled Check** (Optional)
```bash
# Run daily at 9 AM
php artisan inventory:check-low-stock
```

### Stock Status Logic

```php
// Product Model (getStockStatusAttribute)
if ($stock_quantity <= 0) {
    return 'out_of_stock';  // 🔴 Critical
} elseif ($stock_quantity <= $low_stock_threshold) {
    return 'low_stock';     // 🟡 Warning
} else {
    return 'in_stock';      // 🟢 Good
}
```

## Email Notification

### Recipients
The system sends emails to:
1. **Business Email** (if set in business profile)
2. **Owner Email** (fallback if business email not set)

### Email Content

**Subject Line:**
- Low Stock: `⚠️ Low Stock Alert: [Product Name]`
- Out of Stock: `⚠️ Out of Stock Alert: [Product Name]`

**Email Includes:**
- Alert status banner (color-coded)
- Complete product details (Name, SKU, Category, Brand)
- Current stock level (prominently displayed)
- Low stock threshold
- Recommended actions
- Direct "Receive Stock Now" button
- Links to inventory management

### Example Email
```
┌─────────────────────────────────┐
│     ⚠️  LOW STOCK ALERT         │
│     Your Business Name          │
└─────────────────────────────────┘

Product is running low!
Immediate action required to avoid sales disruption.

Product Information
────────────────────
Product Name: iPhone 13 Pro
SKU: IPH13PRO-128
Category: Electronics
Brand: Apple
Low Stock Threshold: 10 units

CURRENT STOCK LEVEL
        5
    units remaining

📋 Recommended Actions:
• Reorder immediately to maintain stock levels
• Contact your supplier to expedite delivery
• Review recent sales trends

[🚚 Receive Stock Now]
```

## Dashboard Notification

### Notification Details
- **Type**: `low_stock`
- **Title**: "Low Stock Alert" or "Out of Stock Alert"
- **Icon**: 
  - Low Stock: `fas fa-exclamation-triangle`
  - Out of Stock: `fas fa-exclamation-circle`
- **Color**:
  - Low Stock: `warning` (yellow/orange)
  - Out of Stock: `danger` (red)

### Notification Data
```json
{
    "product_id": 123,
    "product_name": "iPhone 13 Pro",
    "sku": "IPH13PRO-128",
    "current_stock": 5,
    "low_stock_threshold": 10,
    "category": "Electronics",
    "is_out_of_stock": false
}
```

## Files Created/Modified

### New Files
1. **app/Mail/LowStockAlertMail.php** - Email notification class
2. **resources/views/emails/low-stock-alert.blade.php** - Email template
3. **app/Console/Commands/CheckLowStock.php** - Scheduled command

### Modified Files
1. **app/Services/NotificationService.php** - Added low stock methods
2. **app/Http/Controllers/SalesController.php** - Added trigger after orders
3. **app/Http/Controllers/ProductsController.php** - Added trigger after stock updates

## Usage

### Setting Low Stock Thresholds

When creating or editing products, set the `low_stock_threshold`:

```php
$product->low_stock_threshold = 10; // Alert when stock reaches 10 or below
```

**Default**: If not set, the system uses **10 units** as the default threshold.

### Testing the System

#### Test 1: Manually Trigger Low Stock
```php
// Make a product low stock
$product = Product::find(1);
$product->stock_quantity = 5; // Below threshold
$product->low_stock_threshold = 10;
$product->save();

// Manually trigger notification
$notificationService = new NotificationService();
$notificationService->checkAndNotifyLowStock($product);
```

#### Test 2: Place an Order
1. Go to POS system
2. Add a product to cart
3. Complete the sale
4. If stock falls below threshold, notification is sent automatically

#### Test 3: Manual Stock Update
1. Go to Products → Inventory
2. Update a product's stock to be below threshold
3. Notification is sent automatically

#### Test 4: Run Scheduled Command
```bash
# Check all businesses
php artisan inventory:check-low-stock

# Check specific business
php artisan inventory:check-low-stock --business=76f76ed3-794f-43e8-be94-1087b008895e

# Force send notifications (ignore 24h cooldown)
php artisan inventory:check-low-stock --force
```

## Prevention of Spam

### 24-Hour Cooldown
The system uses caching to prevent sending duplicate notifications within 24 hours:

```php
$cacheKey = "low_stock_notified_{$business_id}_{$product_id}";

// Check if already notified
if (Cache::has($cacheKey)) {
    return null; // Skip notification
}

// Send notification and cache for 24 hours
Cache::put($cacheKey, true, now()->addHours(24));
```

**Benefits:**
- Avoids email spam
- Prevents notification fatigue
- Only alerts once per day per product

### Override Cooldown
Use the `--force` flag with the artisan command:
```bash
php artisan inventory:check-low-stock --force
```

## Scheduled Automation

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Check low stock daily at 9 AM
    $schedule->command('inventory:check-low-stock')
             ->dailyAt('09:00');
    
    // Or check multiple times per day
    $schedule->command('inventory:check-low-stock')
             ->twiceDaily(9, 15); // 9 AM and 3 PM
}
```

## Configuration

### Email Setup
Ensure your `.env` file has email configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Product Configuration
Set appropriate thresholds for each product:

| Product Type | Suggested Threshold |
|-------------|-------------------|
| Fast-moving items | 20-50 units |
| Regular items | 10-20 units |
| Slow-moving items | 5-10 units |
| High-value items | 3-5 units |

## Monitoring & Logs

All low stock checks and notifications are logged:

```php
// Check logs
tail -f storage/logs/laravel.log | grep "Low stock"

// Success logs show:
- Product being checked
- Stock level
- Notification sent confirmation
- Email recipient

// Error logs show:
- Failed notification attempts
- Email sending errors
- Product issues
```

## Benefits

### For Business Owners
✅ **Never Run Out of Stock** - Automatic alerts prevent stockouts
✅ **Timely Reordering** - Know exactly when to reorder
✅ **Lost Sales Prevention** - Avoid losing sales due to out-of-stock items
✅ **Better Planning** - Track which products need frequent reordering
✅ **Professional Management** - Automated, reliable inventory tracking

### For the System
✅ **Automatic** - No manual checking required
✅ **Intelligent** - Prevents spam with cooldown period
✅ **Reliable** - Multiple notification channels
✅ **Actionable** - Direct links to receive stock
✅ **Detailed** - Complete product information included

## Troubleshooting

### Email Not Received?

1. **Check email configuration**:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

2. **Test email sending**:
   ```php
   Mail::raw('Test email', function($message) {
       $message->to('your@email.com')->subject('Test');
   });
   ```

3. **Check logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Notification Not Appearing?

1. **Check if product is actually low**:
   ```php
   $product->stock_status; // Should be 'low_stock' or 'out_of_stock'
   ```

2. **Check cooldown period**:
   ```bash
   # Check cache
   php artisan cache:clear
   
   # Force check
   php artisan inventory:check-low-stock --force
   ```

3. **Verify business has email**:
   ```php
   $business->email ?? $business->user->email; // Should return an email
   ```

## Future Enhancements

Potential improvements:
- **SMS Notifications** - Send SMS alerts for critical items
- **WhatsApp Integration** - Instant messaging alerts
- **Supplier Integration** - Auto-send purchase orders to suppliers
- **Prediction** - AI-based stock prediction and early warnings
- **Custom Schedules** - Per-product notification schedules
- **Multi-recipient** - CC multiple team members
- **Batch Summary** - Daily digest of all low stock items
- **Mobile App Push** - Push notifications to mobile app

## Conclusion

The Low Stock Notification System provides **automatic, intelligent, and professional** inventory management. Business owners can rest assured they'll always know when stock is running low, preventing lost sales and ensuring smooth operations.

**Set it up once, and never worry about running out of stock again!** 🎉








