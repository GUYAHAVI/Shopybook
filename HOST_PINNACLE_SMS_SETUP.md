# 📱 Host Pinnacle SMS Integration Setup Guide

This guide will help you integrate the Host Pinnacle SMS API with your Shopybook application.

---

## 🎯 Prerequisites

1. An active Host Pinnacle account at https://smsportal.hostpinnacle.co.ke
2. SMS credits in your account
3. An approved Sender ID

---

## 🔑 Step 1: Get Your Credentials

### A. Sign Up / Login
1. Go to https://smsportal.hostpinnacle.co.ke
2. Create an account or login
3. Note down your **username** (this is your USER_ID)
4. Note down your **password**

### B. Create and Approve a Sender ID
1. Login to your Host Pinnacle dashboard
2. Navigate to: **Account** → **SenderId** → **Create SenderId**
3. Enter your desired sender name (e.g., "SHOPYBOOK", "MYBUSINESS")
   - Must be 3-11 characters
   - Alphanumeric only
   - Will appear as the SMS sender
4. Submit for approval (may take a few hours to be approved)
5. Once approved, you can use it in your SMS

### C. Optional: Generate API Key
The API key is **optional** but recommended for added security:
1. Navigate to: **Account** → **Api Key** → **Create API Key**
2. Copy and save your API key
3. You can use this in the API header (though it's mostly optional based on the API)

---

## ⚙️ Step 2: Configure Your Application

### Add Credentials to `.env` File

Open your `.env` file in the root of your Shopybook project and add these lines:

```bash
# Host Pinnacle SMS Configuration
HOSTPINNACLE_API_URL=https://smsportal.hostpinnacle.co.ke/SMSApi/send
HOSTPINNACLE_USER_ID=your_username_here
HOSTPINNACLE_PASSWORD=your_password_here
HOSTPINNACLE_SENDER_ID=SHOPYBOOK
HOSTPINNACLE_API_KEY=your_api_key_here_optional
HOSTPINNACLE_RESPONSE_FORMAT=json
```

### Example Configuration:
```bash
HOSTPINNACLE_API_URL=https://smsportal.hostpinnacle.co.ke/SMSApi/send
HOSTPINNACLE_USER_ID=madgenius
HOSTPINNACLE_PASSWORD=MySecureP@ssw0rd
HOSTPINNACLE_SENDER_ID=SHOPYBOOK
HOSTPINNACLE_RESPONSE_FORMAT=json
```

⚠️ **Important:** 
- Replace `your_username_here` with your actual Host Pinnacle username
- Replace `your_password_here` with your actual password
- Replace `SHOPYBOOK` with your approved Sender ID
- The API key is optional

---

## 🧪 Step 3: Test the Integration

### Option A: Using the Web Interface
1. Login to your Shopybook application
2. Navigate to: **Marketing** → **Bulk SMS**
3. Select customers or contact groups
4. Type a test message
5. Click "Send SMS"

### Option B: Using PHP Tinker (Terminal)
```bash
php artisan tinker
```

Then run:
```php
$sms = new \App\Services\HostPinnacleSmsService();

// Check if configured
$sms->isConfigured(); // Should return true

// Send a test SMS
$result = $sms->sendSms('254712345678', 'Test message from Shopybook!');

print_r($result);
```

### Option C: Create a Test Route
Add this temporarily to your `routes/web.php`:

```php
Route::get('/test-sms', function() {
    $sms = new \App\Services\HostPinnacleSmsService();
    
    if (!$sms->isConfigured()) {
        return 'SMS service not configured. Check your .env file.';
    }
    
    $result = $sms->sendSms('254712345678', 'Test SMS from Shopybook!');
    
    return $result;
})->middleware('auth');
```

Then visit: `https://yourapp.com/test-sms`

---

## 📚 API Usage Examples

### 1. Send Single SMS
```php
use App\Services\HostPinnacleSmsService;

$smsService = new HostPinnacleSmsService();

$result = $smsService->sendSms(
    '254712345678',  // Phone number
    'Hello, your order #12345 is ready!' // Message
);
```

### 2. Send Bulk SMS
```php
$phones = ['254712345678', '254723456789', '254734567890'];
$message = 'Special offer: 20% off all items this weekend!';

$result = $smsService->sendBulkSms($phones, $message);
```

### 3. Schedule SMS for Later
```php
$result = $smsService->sendSms(
    '254712345678',
    'Reminder: Your appointment is tomorrow at 10am',
    ['scheduleTime' => '2025-10-08 09:00'] // YYYY-MM-DD HH:MM format
);
```

### 4. Send Unicode SMS (for special characters)
```php
$result = $smsService->sendSms(
    '254712345678',
    'Jambo! Karibu kwa huduma zetu 🎉',
    ['msgType' => 'unicode']
);
```

### 5. Send with Link Tracking
```php
$result = $smsService->sendSms(
    '254712345678',
    'Check out our latest products: https://example.com/products',
    [
        'trackLink' => true,
        'smartLinkTitle' => 'Product Launch Campaign'
    ]
);
```

### 6. Test Mode (No actual SMS sent, just testing)
```php
$result = $smsService->sendSms(
    '254712345678',
    'This is a test',
    ['test' => true] // SMS won't be delivered but API will respond
);
```

### 7. Check Account Status
```php
$status = $smsService->getAccountStatus();
print_r($status);
// Returns credit balance, account info, etc.
```

---

## 📝 Phone Number Formats

The service automatically formats phone numbers. All these formats work:

- `0712345678` → Converted to `254712345678`
- `+254712345678` → Converted to `254712345678`
- `712345678` → Converted to `254712345678`
- `254712345678` → Used as is

---

## 💰 Cost Estimation

The typical cost structure is:
- **Standard SMS (160 characters):** ~KSh 1-2 per SMS
- **Unicode SMS:** May cost more
- **Long messages:** Multiple SMS charges apply (every 160 characters)

Check your Host Pinnacle dashboard for actual pricing.

---

## 🔍 Troubleshooting

### Problem: "SMS service not configured"
**Solution:** 
- Check your `.env` file has the correct credentials
- Run: `php artisan config:clear`
- Restart your server

### Problem: "Invalid Sender ID"
**Solution:**
- Ensure your Sender ID is approved in Host Pinnacle dashboard
- Check spelling matches exactly what's approved
- Sender ID is case-sensitive

### Problem: "Authentication failed"
**Solution:**
- Verify username and password are correct
- Try logging into the Host Pinnacle website with same credentials
- Check for typos in `.env` file

### Problem: "Insufficient credits"
**Solution:**
- Login to Host Pinnacle dashboard
- Check your SMS credit balance
- Top up your account if needed

### Problem: SMS not delivered
**Solution:**
- Check phone numbers are in correct format (254XXXXXXXXX)
- Verify phone numbers are valid and active
- Check SMS delivery report in Host Pinnacle dashboard
- Ensure message doesn't contain spam words

### Problem: "Connection timeout"
**Solution:**
- Check your server has internet access
- Verify firewall isn't blocking outgoing connections
- Test API URL manually: `https://smsportal.hostpinnacle.co.ke/SMSApi/send`

---

## 📊 View Logs

Check Laravel logs for detailed SMS activity:
```bash
tail -f storage/logs/laravel.log | grep SMS
```

Or view in your application at: `storage/logs/laravel.log`

---

## 🔐 Security Best Practices

1. **Never commit `.env` file** to version control
2. **Use environment-specific credentials** (different for dev/production)
3. **Rotate passwords regularly**
4. **Monitor SMS usage** to detect unauthorized access
5. **Set spending limits** in Host Pinnacle dashboard
6. **Validate user input** before sending SMS
7. **Implement rate limiting** to prevent abuse

---

## 📞 Support

### Host Pinnacle Support
- **Website:** https://smsportal.hostpinnacle.co.ke
- **Dashboard:** Login → Support/Help section
- **Documentation:** Available in your account

### Shopybook Integration Support
- Check `storage/logs/laravel.log` for errors
- Review this guide
- Test with the examples provided

---

## 🎉 You're All Set!

Your SMS integration is now ready. You can:
- ✅ Send single SMS
- ✅ Send bulk SMS to customers
- ✅ Schedule SMS for later
- ✅ Send SMS to contact groups
- ✅ Track link clicks
- ✅ Send unicode messages
- ✅ Check account balance

Happy texting! 📱💬

