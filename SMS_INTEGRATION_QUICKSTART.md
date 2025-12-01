# 📱 SMS Integration Quick Start

## ✅ Credentials You Need

From https://smsportal.hostpinnacle.co.ke:

| Credential | What is it? | Required? |
|------------|-------------|-----------|
| **USER_ID** | Your account username | ✅ Yes |
| **PASSWORD** | Your account password | ✅ Yes |
| **SENDER_ID** | Your approved sender name (e.g., "SHOPYBOOK") | ✅ Yes |
| **API_KEY** | Optional API key for extra security | ⚪ Optional |

## 🚀 Quick Setup (3 Steps)

### Step 1: Get Your Sender ID Approved
1. Login to https://smsportal.hostpinnacle.co.ke
2. Go to: **Account** → **SenderId** → **Create SenderId**
3. Enter your sender name (3-11 characters, e.g., "SHOPYBOOK")
4. Wait for approval (usually within a few hours)

### Step 2: Add to `.env` File
```bash
HOSTPINNACLE_USER_ID=your_username
HOSTPINNACLE_PASSWORD=your_password
HOSTPINNACLE_SENDER_ID=SHOPYBOOK
```

### Step 3: Test It
```bash
php test_sms_integration.php
```

## 📝 .ENV Template

Copy these lines to your `.env` file and replace with your actual values:

```env
# Host Pinnacle SMS Configuration
HOSTPINNACLE_API_URL=https://smsportal.hostpinnacle.co.ke/SMSApi/send
HOSTPINNACLE_USER_ID=your_username_here
HOSTPINNACLE_PASSWORD=your_password_here
HOSTPINNACLE_SENDER_ID=SHOPYBOOK
HOSTPINNACLE_RESPONSE_FORMAT=json
```

## 🔑 Do You Need an API Key?

**Short answer: NO**, the API key is optional!

Based on the Postman collection you provided, the Host Pinnacle API primarily uses **username (USER_ID) and password** for authentication. The API key is optional and disabled in most endpoints.

**What you absolutely need:**
- ✅ Username (USER_ID)
- ✅ Password
- ✅ Approved Sender ID

**What's optional:**
- ⚪ API Key (for additional security layer)

## 💡 Quick Code Examples

### Send Single SMS
```php
use App\Services\HostPinnacleSmsService;

$sms = new HostPinnacleSmsService();
$result = $sms->sendSms('254712345678', 'Hello from Shopybook!');
```

### Send Bulk SMS
```php
$phones = ['254712345678', '254723456789'];
$result = $sms->sendBulkSms($phones, 'Special offer: 20% off!');
```

### Schedule SMS
```php
$result = $sms->sendSms(
    '254712345678', 
    'Reminder: Meeting tomorrow',
    ['scheduleTime' => '2025-10-08 09:00']
);
```

### Check Account Balance
```php
$status = $sms->getAccountStatus();
print_r($status);
```

## 🎯 Using in Your App

The bulk SMS feature is already integrated! Just:

1. Login to your Shopybook application
2. Navigate to: **Marketing** → **Bulk SMS**
3. Select customers
4. Write your message
5. Click "Send SMS"

## ❓ Common Issues

### "SMS service not configured"
→ Check your `.env` file has all three credentials  
→ Run: `php artisan config:clear`

### "Invalid Sender ID"
→ Make sure your Sender ID is approved in the dashboard  
→ Check spelling matches exactly

### "Authentication failed"
→ Verify username and password are correct  
→ Try logging into Host Pinnacle website with same credentials

### "Insufficient credits"
→ Login to Host Pinnacle dashboard  
→ Check your balance and top up if needed

## 📚 Full Documentation

For detailed information, see:
- **HOST_PINNACLE_SMS_SETUP.md** - Complete setup guide with all features
- **smsportal_hostpinnacle_co_ke_collection.json** - Full API reference

## 🧪 Testing Checklist

- [ ] Added credentials to `.env` file
- [ ] Cleared config cache: `php artisan config:clear`
- [ ] Sender ID is approved in Host Pinnacle dashboard
- [ ] Ran test script: `php test_sms_integration.php`
- [ ] Successfully sent test SMS
- [ ] Checked logs: `storage/logs/laravel.log`
- [ ] Tested in web interface: Marketing → Bulk SMS

## 💰 Pricing

Typical costs (check your Host Pinnacle account for exact pricing):
- Standard SMS: ~KSh 1-2 per message
- Messages over 160 characters count as multiple SMS
- Unicode/special characters may cost more

## 📞 Need Help?

1. Check `storage/logs/laravel.log` for detailed error messages
2. Review the **Troubleshooting** section in HOST_PINNACLE_SMS_SETUP.md
3. Verify credentials work by logging into Host Pinnacle website
4. Ensure you have SMS credits in your account

---

**That's it! You're ready to send SMS! 🎉**



