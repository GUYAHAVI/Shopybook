# Notification Troubleshooting Guide for cPanel Deployment

## Problem
Notifications (low stock alerts, order notifications) work on localhost but don't appear when deployed to cPanel, and no error messages are shown.

## Why This Happens
Your notification code catches all exceptions and logs them instead of displaying them. On cPanel, several configuration differences can cause silent failures.

---

## Step 1: Run the Debug Endpoint

Visit this URL on your cPanel deployment:
```
https://your-domain.com/debug-notifications
```

This will show you:
- ✅ Storage folder writability
- ✅ Cache system working
- ✅ Database connection
- ✅ Notifications table existence
- ✅ Recent notifications
- ✅ Recent errors from logs

**Save this output for analysis.**

---

## Step 2: Fix Common cPanel Issues

### 🔧 Issue 1: Storage Folder Permissions (Most Common)

**Problem:** Laravel can't write to `storage/logs/` or `storage/framework/cache/`

**Fix via cPanel Terminal/SSH:**
```bash
cd /home/your_username/public_html/your_app
chmod -R 775 storage bootstrap/cache
chown -R your_username:your_username storage bootstrap/cache
```

**Fix via cPanel File Manager:**
1. Right-click on `storage` folder → "Change Permissions"
2. Set to `775` (or check all: Read, Write, Execute for Owner and Group)
3. Check "Recurse into subdirectories"
4. Click "Change Permissions"
5. Repeat for `bootstrap/cache`

---

### 🔧 Issue 2: Cache Configuration

**Problem:** Your `.env` file might be using `redis` or `memcached` cache that isn't available on cPanel.

**Fix - Edit `.env` file on cPanel:**
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

**Then run these commands via cPanel Terminal:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

### 🔧 Issue 3: Database Not Migrated

**Problem:** The `notifications` table doesn't exist in your production database.

**Check via phpMyAdmin:**
1. Go to cPanel → phpMyAdmin
2. Select your database
3. Look for a table named `notifications`

**If missing, run migration:**
```bash
php artisan migrate
```

**If you get errors, run:**
```bash
php artisan migrate:status
php artisan migrate --force
```

---

### 🔧 Issue 4: Mail Configuration

**Problem:** Email notifications fail silently due to incorrect SMTP settings.

**Check your `.env` file on cPanel:**
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com  # Use cPanel's mail server
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com  # Must be a real email on your cPanel
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**For cPanel specifically:**
- Use your domain's mail server (e.g., `mail.yourdomain.com`)
- Port `587` with `tls` encryption
- Create the email account in cPanel → Email Accounts first
- Use the full email address as username

**After changing, clear config:**
```bash
php artisan config:clear
```

---

### 🔧 Issue 5: Environment Not Set

**Check `.env` file:**
```env
APP_ENV=production
APP_DEBUG=false  # Set to true temporarily to see errors
LOG_LEVEL=debug
```

**To temporarily see errors (REMOVE AFTER DEBUGGING):**
```env
APP_DEBUG=true
```

Then run:
```bash
php artisan config:clear
```

---

## Step 3: Check Laravel Logs

The real errors are logged even if they're not displayed.

**Via cPanel File Manager:**
1. Navigate to: `/storage/logs/`
2. Download `laravel.log`
3. Open in text editor
4. Search for:
   - `"Failed to send"`
   - `"notification"`
   - `"error"`
   - `"exception"`

**Via SSH:**
```bash
# View last 100 lines
tail -100 storage/logs/laravel.log

# Search for notification errors
grep -i "notification" storage/logs/laravel.log | tail -50

# Search for any errors today
grep "$(date '+%Y-%m-%d')" storage/logs/laravel.log | grep -i "error"
```

---

## Step 4: Test Notification Manually

Create a test route to manually trigger a notification:

**Add to `routes/web.php`:**
```php
Route::get('/test-notification', function() {
    $product = \App\Models\Product::first();
    if (!$product) {
        return 'No products found in database';
    }
    
    try {
        $notification = \App\Models\Notification::create([
            'business_id' => $product->business_id,
            'type' => 'test',
            'title' => 'Test Notification',
            'message' => 'This is a test notification created at ' . now(),
            'data' => ['test' => true],
            'icon' => 'fas fa-bell',
            'color' => 'info'
        ]);
        
        return 'Notification created successfully! ID: ' . $notification->id;
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->name('test.notification');
```

Visit: `https://your-domain.com/test-notification`

---

## Step 5: Common cPanel-Specific Issues

### PHP Version
- Ensure you're using PHP 8.1 or higher
- Check: cPanel → MultiPHP Manager

### PHP Extensions Required
Go to cPanel → PHP Extensions and enable:
- ✅ pdo_mysql
- ✅ mbstring
- ✅ json
- ✅ openssl
- ✅ tokenizer

### File Paths
cPanel structure is usually:
```
/home/username/
    public_html/
        (your Laravel app files here)
        or
        your_app_folder/
```

Make sure your app is properly linked:
- `public_html` should point to Laravel's `public` folder
- Or use `.htaccess` redirect

---

## Step 6: Check Notification Frontend Display

The notifications might be created but not displayed in the UI.

**Check in Browser Console (F12):**
```javascript
// Check if notifications are loaded
console.log(document.querySelectorAll('.notification-item').length);
```

**Check the notification count endpoint:**
Visit: `https://your-domain.com/notifications/unread-count`

---

## Quick Checklist

- [ ] Storage folder has 775 permissions
- [ ] `.env` has `CACHE_DRIVER=file`
- [ ] `.env` has correct mail settings
- [ ] Database has `notifications` table
- [ ] Checked `storage/logs/laravel.log` for errors
- [ ] Ran `php artisan config:clear`
- [ ] Ran `php artisan cache:clear`
- [ ] PHP 8.1+ is active
- [ ] Required PHP extensions are enabled
- [ ] `/debug-notifications` endpoint shows no errors

---

## Still Not Working?

1. **Enable debug mode temporarily:**
   ```env
   APP_DEBUG=true
   ```
   Then try creating an order/sale to see the actual error.

2. **Check failed_jobs table:**
   ```sql
   SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
   ```

3. **Contact your cPanel host:**
   - Ask if there are any restrictions on PHP mail() function
   - Ask if storage folders need special permissions
   - Ask about any mod_security rules blocking requests

---

## After Fixing

Once everything works:

1. **Remove debug routes:**
   - Remove `/debug-notifications` route from `routes/web.php`
   - Remove `/test-notification` route if you added it

2. **Set production values:**
   ```env
   APP_DEBUG=false
   APP_ENV=production
   ```

3. **Clear everything:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   php artisan optimize
   ```

---

## Prevention for Future Deployments

Create a deployment checklist:
```bash
php artisan migrate --force
php artisan storage:link
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
chmod -R 775 storage bootstrap/cache
```

Good luck! 🚀





