# Chatbot Debugging Guide

## Recent Updates (December 1, 2025)

### Changes Made:
1. ✅ Changed bot message text color to **black (#000)**
2. ✅ Added comprehensive **frontend logging** (JavaScript console)
3. ✅ Added comprehensive **backend logging** (Laravel logs)

## How to Debug the Chatbot

### Step 1: Open Browser Console
1. Visit: https://shopybook.com
2. Press `F12` or `Right-click → Inspect`
3. Go to **Console** tab
4. Click the chatbot button

### Step 2: Test a Message
Type a test message like: "What is Shopybook?"

### Step 3: Check Console Logs
You should see logs like:
```
[Chatbot] Sending message: What is Shopybook?
[Chatbot] Fetching response from: https://shopybook.com/chatbot/message
[Chatbot] CSRF Token: [token-value]
[Chatbot] Response status: 200 OK
[Chatbot] Response data: {...}
[Chatbot] Success - displaying response
[Chatbot] Adding message: {...}
[Chatbot] Message added successfully
```

### Step 4: Check for Errors
If you see errors, they will be logged with details:
```
[Chatbot] Fetch error: [error details]
[Chatbot] Error details: {name, message, stack}
```

### Step 5: Check Laravel Logs
Open terminal and run:
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50 -Wait

# Or view the entire log file
notepad storage/logs/laravel.log
```

Look for entries with `[Chatbot]` prefix:
```
[2025-12-01 10:30:00] local.INFO: [Chatbot] Received chat request {...}
[2025-12-01 10:30:01] local.INFO: [Chatbot] User message {...}
[2025-12-01 10:30:02] local.INFO: [Chatbot] API key found, length: 108
[2025-12-01 10:30:03] local.INFO: [Chatbot] Calling Claude API {...}
[2025-12-01 10:30:05] local.INFO: [Chatbot] Claude API response status: 200
[2025-12-01 10:30:05] local.INFO: [Chatbot] Sending success response {...}
```

## Common Issues & Solutions

### Issue 1: "Sorry, I encountered an error"
**Possible Causes:**
- Claude API key invalid or expired
- Network connectivity issues
- API rate limit exceeded
- Malformed API request

**Check:**
1. Browser Console for exact error
2. Laravel logs for detailed error message
3. API key validity in `.env`

### Issue 2: No Response
**Possible Causes:**
- JavaScript error preventing request
- CSRF token missing
- Route not registered

**Check:**
1. Browser Console for JavaScript errors
2. Network tab for failed requests
3. Laravel logs for incoming requests

### Issue 3: Text Color Still Not Black
**Possible Causes:**
- Browser cache
- CSS specificity issues

**Solutions:**
1. Hard refresh: `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)
2. Clear browser cache
3. Check with browser inspector on the `<p>` tag

### Issue 4: CSRF Token Error
**Possible Causes:**
- Missing meta tag in layout
- Session expired

**Solutions:**
1. Verify meta tag exists in `layouts/master.blade.php`:
   ```html
   <meta name="csrf-token" content="{{ csrf_token() }}">
   ```
2. Refresh the page to get new token

## Verification Checklist

Before reporting issues, verify:

- [ ] Browser console is open and showing logs
- [ ] Laravel logs are being written (`storage/logs/laravel.log`)
- [ ] CSRF meta tag exists in page source
- [ ] Claude API key is set in `.env`
- [ ] Internet connection is working
- [ ] Chatbot button appears on page
- [ ] Click on chatbot button opens chat window
- [ ] Can type in the input field
- [ ] Send button is clickable

## Expected Log Flow

### Successful Request:
```
Frontend:
[Chatbot] Sending message: test
[Chatbot] Fetching response from: /chatbot/message
[Chatbot] CSRF Token: abc123...
[Chatbot] Response status: 200 OK
[Chatbot] Response data: {success: true, response: "..."}
[Chatbot] Success - displaying response
[Chatbot] Adding message: {text: "...", sender: "bot"}
[Chatbot] Message added successfully

Backend:
[Chatbot] Received chat request {ip, user_agent, message_length}
[Chatbot] User message {message}
[Chatbot] API key found, length: 108
[Chatbot] System prompt length: 5420
[Chatbot] Calling Claude API {model, max_tokens}
[Chatbot] Claude API response status: 200
[Chatbot] Claude API response received {has_content, content_count}
[Chatbot] Sending success response {response_length}
```

### Failed Request:
```
Frontend:
[Chatbot] Sending message: test
[Chatbot] Fetching response from: /chatbot/message
[Chatbot] CSRF Token: abc123...
[Chatbot] Response status: 500 Internal Server Error
[Chatbot] Response data: {success: false, message: "..."}
[Chatbot] Error response: {...}

Backend:
[Chatbot] Received chat request {...}
[Chatbot] User message {...}
[Chatbot] Exception occurred {message, file, line, trace}
```

## Testing Commands

### Test 1: Check if route is accessible
```bash
# PowerShell
Invoke-WebRequest -Uri "https://shopybook.com/chatbot/message" `
  -Method POST `
  -Headers @{"Content-Type"="application/json"} `
  -Body '{"message":"test"}'
```

### Test 2: Check Laravel logs in real-time
```bash
# PowerShell
Get-Content storage/logs/laravel.log -Tail 20 -Wait
```

### Test 3: Check if API key is set
```bash
# PowerShell
php artisan tinker
>>> env('CLAUDE_API_KEY')
# Should show: "sk-ant-api03-..."
```

### Test 4: Clear all caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Quick Diagnostic Script

Create a test file `test-chatbot.php` in your root directory:
```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Chatbot Configuration\n";
echo "==============================\n\n";

echo "1. CLAUDE_API_KEY: " . (env('CLAUDE_API_KEY') ? "✓ Set (length: " . strlen(env('CLAUDE_API_KEY')) . ")" : "✗ Not set") . "\n";
echo "2. APP_URL: " . env('APP_URL') . "\n";
echo "3. APP_ENV: " . env('APP_ENV') . "\n";
echo "4. APP_DEBUG: " . (env('APP_DEBUG') ? "✓ Enabled" : "✗ Disabled") . "\n";
echo "5. Route exists: " . (\Illuminate\Support\Facades\Route::has('chatbot.message') ? "✓ Yes" : "✗ No") . "\n";

echo "\nTest making API request...\n";
try {
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'x-api-key' => env('CLAUDE_API_KEY'),
        'anthropic-version' => '2023-06-01',
        'content-type' => 'application/json',
    ])->timeout(10)->post('https://api.anthropic.com/v1/messages', [
        'model' => 'claude-3-5-sonnet-20241022',
        'max_tokens' => 100,
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Say "Hello" only.'
            ]
        ]
    ]);
    
    echo "API Response Status: " . $response->status() . "\n";
    if ($response->successful()) {
        echo "✓ API is working!\n";
    } else {
        echo "✗ API error: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}
```

Run with:
```bash
php test-chatbot.php
```

## Contact Support

If issues persist after checking logs:

1. **Email:** info@shopybook.com
2. **Phone/WhatsApp:** +254 717745891
3. **Include:**
   - Browser console logs (screenshot or copy)
   - Laravel logs (last 50 lines)
   - Error message
   - Steps to reproduce

---

**Last Updated:** December 1, 2025
**Author:** GitHub Copilot
**Version:** 1.1.0 (with debugging)
