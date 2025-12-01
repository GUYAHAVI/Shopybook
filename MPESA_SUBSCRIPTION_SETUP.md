# M-Pesa Subscription Integration - Setup Guide

## Overview
The subscription system has been fully integrated with M-Pesa STK Push for seamless payment processing. Users can upgrade to Premium (KSH 500/month) or Enterprise (KSH 1,000/month) plans directly from the dashboard or settings page.

## Pricing
- **Premium Plan**: KSH 500/month
  - AI Website Builder
  - Advanced Analytics
  - Email Support
  - Up to 1,000 products

- **Enterprise Plan**: KSH 1,000/month (Most Popular)
  - Everything in Premium
  - Priority Support (2 hours response)
  - Unlimited Products
  - Custom Branding
  - API Access

## Environment Configuration

Add these M-Pesa Daraja API credentials to your `.env` file:

```env
# M-Pesa Daraja API Configuration (Sandbox)
MPESA_CONSUMER_KEY=your_consumer_key_here
MPESA_CONSUMER_SECRET=your_consumer_secret_here
MPESA_BUSINESS_SHORTCODE=174379
MPESA_PASSKEY=your_passkey_here
MPESA_ENVIRONMENT=sandbox

# M-Pesa URLs (these are already set as defaults, but you can override)
MPESA_AUTH_URL=https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials
MPESA_STK_PUSH_URL=https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest

# M-Pesa Callback URL (IMPORTANT: Update this with your actual domain)
MPESA_CALLBACK_URL=https://yourdomain.com/subscription/mpesa/callback

# Transaction Settings
MPESA_TRANSACTION_TYPE=CustomerPayBillOnline
MPESA_ACCOUNT_REFERENCE=Shopybook Premium
MPESA_TRANSACTION_DESC=Premium Plan Upgrade
```

### For Production (Live Environment)
When ready to go live, update these values:

```env
MPESA_ENVIRONMENT=live
MPESA_AUTH_URL=https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials
MPESA_STK_PUSH_URL=https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest
MPESA_CALLBACK_URL=https://yourdomain.com/subscription/mpesa/callback
# Use your LIVE credentials
MPESA_CONSUMER_KEY=your_live_consumer_key
MPESA_CONSUMER_SECRET=your_live_consumer_secret
MPESA_BUSINESS_SHORTCODE=your_live_shortcode
MPESA_PASSKEY=your_live_passkey
```

## How It Works

### 1. User Flow
1. User navigates to Dashboard or Settings > Subscription tab
2. Clicks "Upgrade Now" button
3. Selects desired plan (Premium or Enterprise)
4. Enters M-Pesa phone number (07XXXXXXXX or 254XXXXXXXXX)
5. Clicks "Pay with M-Pesa"
6. Receives STK push notification on their phone
7. Enters M-Pesa PIN to complete payment
8. Business plan is automatically upgraded upon successful payment

### 2. Technical Flow
1. **Frontend**: User submits upgrade form from `resources/views/business/settings/index.blade.php` or dashboard modal
2. **Controller**: `SubscriptionController@upgrade` validates request and formats phone number
3. **Authentication**: Gets Daraja access token using consumer key/secret
4. **STK Push**: Initiates payment request to user's phone
5. **Database**: Stores pending payment record in `subscription_payments` table
6. **Callback**: M-Pesa calls `/subscription/mpesa/callback` with payment result
7. **Upgrade**: Controller updates business plan and marks payment as completed

### 3. Database Schema
```sql
CREATE TABLE subscription_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id VARCHAR(255) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    plan ENUM('premium', 'enterprise') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    checkout_request_id VARCHAR(255) UNIQUE NOT NULL,
    merchant_request_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    mpesa_receipt_number VARCHAR(255),
    transaction_date VARCHAR(255),
    result_desc TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(business_id),
    INDEX(status),
    INDEX(checkout_request_id)
);
```

## Testing with Sandbox

### Test Credentials
Use Safaricom's test credentials from your Daraja account:
- **Test Phone Numbers**: Use your actual Safaricom number for testing
- **Test Amount**: The system will send actual amounts (500 or 1000 KSH in sandbox mode)
- **Test Shortcode**: Usually 174379 for sandbox

### Testing Steps
1. Ensure `.env` has sandbox credentials configured
2. Run `php artisan config:clear` to refresh configuration
3. Navigate to Dashboard or Settings > Subscription
4. Click "Upgrade Now"
5. Select a plan (Premium or Enterprise)
6. Enter your Safaricom test number
7. Click "Pay with M-Pesa"
8. Check your phone for STK push
9. Enter your M-Pesa PIN (use test PIN in sandbox: 1234 if prompted)
10. Wait for confirmation message
11. Verify business plan was upgraded in Settings

### Monitoring Payments
Check logs for payment processing:
```bash
tail -f storage/logs/laravel.log | grep -i "subscription\|mpesa\|stk"
```

Or query the database:
```sql
SELECT * FROM subscription_payments ORDER BY created_at DESC LIMIT 10;
```

## Updated Files

### New Files
- `app/Http/Controllers/SubscriptionController.php` - Handles subscription upgrades and M-Pesa callbacks
- `database/migrations/2025_11_26_123625_create_subscription_payments_table.php` - Payment records table

### Modified Files
- `resources/views/dashboard.blade.php` - Updated pricing to KSH, changed modal pricing
- `resources/views/business/settings/index.blade.php` - Added plan selection, M-Pesa payment form, and JavaScript
- `routes/web.php` - Added subscription routes
- `config/services.php` - Updated M-Pesa callback URL
- `bootstrap/app.php` - Excluded M-Pesa callback from CSRF protection

## Routes

### Protected Routes (requires authentication)
- `POST /subscription/upgrade` - Process subscription upgrade and initiate STK push

### Public Routes (M-Pesa callbacks)
- `POST /subscription/mpesa/callback` - Receive payment confirmations from Safaricom

## Important Notes

### Callback URL
The callback URL **MUST** be publicly accessible for M-Pesa to call it. For local testing:
- Use ngrok or similar tunneling service: `ngrok http 8000`
- Update `MPESA_CALLBACK_URL` in `.env` with the ngrok URL
- Example: `MPESA_CALLBACK_URL=https://abc123.ngrok.io/subscription/mpesa/callback`

### Phone Number Format
The system accepts both formats and automatically converts:
- `07XXXXXXXX` → `254XXXXXXXXX`
- `254XXXXXXXXX` → `254XXXXXXXXX` (already correct)

### Security
- Callback route is exempt from CSRF protection (required for M-Pesa)
- All payment data is logged for audit purposes
- Failed payments are marked in database for reconciliation

### Error Handling
The system handles common errors:
- Invalid phone number format
- M-Pesa authentication failures
- Network timeouts
- Payment cancellations by user
- Duplicate payments

All errors are logged to `storage/logs/laravel.log` with context.

## Troubleshooting

### "Failed to connect to M-Pesa"
- Check consumer key/secret in `.env`
- Verify credentials are for correct environment (sandbox vs live)
- Run `php artisan config:clear`

### "Payment request sent but no STK push received"
- Verify phone number is correct Safaricom number
- Check if number is active and can receive STK push
- Verify business shortcode is correct
- Check M-Pesa logs in Daraja portal

### "Callback not being received"
- Ensure callback URL is publicly accessible
- Check callback URL is correct in Daraja portal settings
- Verify CSRF exemption is working
- Check web server logs for incoming requests

### Payment succeeded but plan not upgraded
- Check `subscription_payments` table for payment status
- Review Laravel logs for callback processing errors
- Manually verify M-Pesa receipt number in Daraja portal
- Check business table for plan column update

## Support

For M-Pesa integration issues:
- Visit: https://developer.safaricom.co.ke
- Email: apisupport@safaricom.co.ke

For Shopybook subscription issues, check the logs and payment records in the database.
