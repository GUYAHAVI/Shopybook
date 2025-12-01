# Critical Files to Update on cPanel

## Files That MUST Be Updated

### 1. SubscriptionController.php
**Location:** `app/Http/Controllers/SubscriptionController.php`

**Critical Change:** Returns JSON response instead of redirect
- Lines 98-117 changed
- Now returns `response()->json()` for AJAX requests
- Prevents page reload when payment is initiated

**What it does:**
- When form is submitted via AJAX, returns JSON with payment reference
- Modal stays open and JavaScript starts polling
- No page redirect happens

---

### 2. dashboard.blade.php
**Location:** `resources/views/dashboard.blade.php`

**Critical Changes:**
1. **AJAX Form Submission** (Lines ~1773-1823)
   - Prevents default form submission
   - Submits via `fetch()` API
   - Calls `startPaymentStatusPolling()` with reference from response
   
2. **Enhanced Polling Logic** (Lines ~1790-1960)
   - Locks modal during payment
   - Shows real-time status with elapsed time
   - Handles success/failure/timeout states
   - Re-enables form on failure for retry
   
3. **Pricing Fix** (Lines ~1642, ~1668)
   - Premium: KSH 500 (was KSH 5)
   - Enterprise: KSH 1,000 (was KSH 10)

**What it does:**
- Intercepts form submit and uses AJAX instead
- Keeps modal open during entire payment flow
- Shows spinner and status messages
- Only redirects on success after 2 seconds
- Allows retry on failure

---

## How to Update Files on cPanel

### Option 1: File Manager (Recommended)
1. Login to cPanel
2. Go to File Manager
3. Navigate to your Laravel root folder (usually `public_html` or `shopybook`)
4. For each file:
   - Right-click → Edit
   - Replace entire file content with new version
   - Save

### Option 2: FTP/SFTP
1. Use FileZilla or similar FTP client
2. Connect to your server
3. Upload the updated files to overwrite existing ones

### Option 3: Git Pull (If using Git)
```bash
cd /home/your-username/shopybook
git pull origin main
php artisan config:clear
php artisan view:clear
```

---

## After Updating Files

Run these commands in Terminal (cPanel or SSH):

```bash
cd /home/your-username/shopybook
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

---

## Testing Checklist

After updating files, test this flow:

1. **Login to dashboard**
2. **Click "Upgrade Now"** button in banner
3. **Select Enterprise plan** (KES 1,000)
4. **Enter M-Pesa number:** 0712345678
5. **Click "Pay with M-Pesa"**

**Expected Behavior:**
- ✅ Modal should NOT close
- ✅ Submit button changes to "Sending STK Push..."
- ✅ Status message appears: "Waiting for M-Pesa payment..."
- ✅ You receive STK push on phone
- ✅ After entering PIN, status updates every 2 seconds
- ✅ On success: Green message → Redirects after 2 seconds
- ✅ On failure: Red message → Form re-enables → Can retry

**If It Still Reloads:**
- Check browser console (F12) for JavaScript errors
- Verify you updated BOTH files on cPanel
- Clear browser cache (Ctrl+Shift+Delete)
- Try in incognito/private window

---

## Quick Fix Summary

**Before (OLD CODE):**
- Form submits normally → Page redirects immediately
- User loses track of payment status
- Modal closes before payment completes

**After (NEW CODE):**
- Form submits via AJAX → No redirect
- Modal stays open and locked
- Real-time status updates
- Redirect only on success

---

## File Sizes (For Verification)

After updating, verify file sizes are approximately:

- `SubscriptionController.php`: ~12-15 KB
- `dashboard.blade.php`: ~85-90 KB

If file sizes are significantly different, the update may not have worked correctly.

---

## Common Issues

### Issue: "Modal still closes immediately"
**Solution:** Clear browser cache completely or try incognito mode

### Issue: "Form doesn't submit at all"
**Solution:** Check browser console for JavaScript errors - likely the dashboard.blade.php wasn't updated

### Issue: "Gets error 500"
**Solution:** Check Laravel logs at `storage/logs/laravel.log` - likely SubscriptionController.php syntax error

### Issue: "Shows old prices (KES 5/10)"
**Solution:** Browser cache - hard refresh with Ctrl+F5 or clear cache

---

## Support

If issues persist after updating both files:

1. Check `storage/logs/laravel.log` for PHP errors
2. Check browser console (F12) for JavaScript errors
3. Verify both files were actually updated (check file modified timestamps)
4. Ensure you ran the cache clear commands

The payment flow will ONLY work correctly when BOTH files are updated on the server.
