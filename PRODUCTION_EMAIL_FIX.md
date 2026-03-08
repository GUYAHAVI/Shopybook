# PRODUCTION EMAIL FIX FOR SHARED HOSTING

## Problem
Verification emails not sending in production because:
- QUEUE_CONNECTION=database
- Can't run `php artisan queue:work` continuously on shared hosting
- Terminal closes when cPanel closes

## Solution: Cron Job (Recommended)

### Step 1: Create Cron Job in cPanel

1. Log in to **cPanel**
2. Go to **Cron Jobs** (under Advanced section)
3. Add a new cron job:

**Command:**
```bash
* * * * * cd /home/YOUR_CPANEL_USERNAME/shopybook_directory && /usr/bin/php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

**Important:** Replace:
- `YOUR_CPANEL_USERNAME` with your actual cPanel username
- `shopybook_directory` with your actual directory (usually `public_html` or `shopybook.com`)
- `/usr/bin/php` might be `/usr/local/bin/php` or `/opt/cpanel/ea-php82/root/usr/bin/php` (check with `which php` in terminal)

**Schedule:** Every 1 minute (cPanel default: `* * * * *`)

This runs every minute, processes all queued jobs, then stops. It won't leave processes hanging.

### Step 2: Verify Cron is Working

After 2-3 minutes, check if jobs are being processed:
```bash
php artisan queue:failed
```

If no failed jobs and emails are sending, it's working!

### Alternative: Direct Send (Simpler but slower)

If cron doesn't work, change **production** `.env`:
```env
QUEUE_CONNECTION=sync
```

Then run on server:
```bash
php artisan config:clear
php artisan cache:clear
```

**Pros:** Emails send immediately
**Cons:** Slower page loads (user waits for email to send)

## Testing in Production

1. Register a new test user with Gmail
2. Check if verification email arrives within 1-2 minutes
3. Check cPanel cron job logs for errors

## Common cPanel PHP Paths

Try these if `/usr/bin/php` doesn't work:
- `/usr/local/bin/php`
- `/opt/cpanel/ea-php82/root/usr/bin/php`
- `/opt/alt/php82/usr/bin/php`

Run `which php` in cPanel terminal to find yours.

## Which Solution?

**Use Cron** if you want:
- Fast page loads
- Professional setup
- Can handle multiple queued emails

**Use sync** if you:
- Can't get cron working
- Have low email volume
- Want immediate sends

For Shopybook, I recommend **cron** since you'll have order notifications, verification emails, etc.
