# 🔧 Storage Recovery Toolkit - Complete Solution

## 📌 Quick Start

You accidentally deleted the `storage` folder. This toolkit helps you:
1. ✅ Recreate the storage structure
2. ✅ Identify missing images
3. ✅ Clean database references
4. ✅ Notify affected users
5. ✅ Prevent future issues

---

## 🎯 One-Command Fix (Local Development)

```bash
# Step 1: Check what's missing
php artisan images:cleanup-missing --dry-run

# Step 2: Fix database references
php artisan images:cleanup-missing --reset

# Step 3: Done! Users will see default placeholders until they re-upload
```

---

## 📦 Files Included in This Toolkit

### Artisan Commands (Ready to Use)
1. **`app/Console/Commands/CleanupMissingImages.php`**
   - Scans for missing image files
   - Resets broken database references
   - Works for both business logos and product images
   
2. **`app/Console/Commands/NotifyMissingImages.php`**
   - Sends email notifications to affected users
   - Preview mode to see who will be notified
   - Test email option

### Web Scripts (For cPanel)
1. **`recreate-storage-structure.php`**
   - Recreates all Laravel storage directories
   - Sets correct permissions
   - One-time use, then delete

2. **`check-missing-images.php`**
   - Generates HTML/CSV report of missing images
   - Shows affected businesses and products
   - One-time use, then delete

### UI Updates
1. **`resources/views/dashboard.blade.php`**
   - Added alert banner for users with missing logos
   - Links directly to upload page

2. **`resources/views/emails/missing-logo-notification.blade.php`**
   - Professional email template
   - Clear instructions for users
   - Support contact information

### Documentation
1. **`STORAGE-RECOVERY-GUIDE.md`** - Complete detailed guide
2. **`STORAGE-RECOVERY-SUMMARY.md`** - Quick reference
3. **`README-STORAGE-RECOVERY.md`** - This file

---

## 🚀 Deployment Instructions

### Option A: Local Development (You Are Here)

```bash
# 1. Check current status
php artisan images:cleanup-missing --dry-run

# Output shows:
# - 5 missing business logos
# - 1 missing product image

# 2. Clean database (removes broken references)
php artisan images:cleanup-missing --reset

# 3. View who needs notification
php artisan images:notify-missing

# 4. (Optional) Send emails
php artisan images:notify-missing --send

# 5. Test the dashboard
# - Log in as a user with missing logo
# - You'll see the warning banner
# - Click "Upload Logo Now" button
```

### Option B: cPanel Production Server

```bash
# 1. Commit and push your changes
git add .
git commit -m "Add storage recovery toolkit"
git push

# 2. On cPanel, pull latest code
cd /home/username/yourproject
git pull

# 3. Upload helper scripts to project root
# - Upload recreate-storage-structure.php
# - Upload check-missing-images.php

# 4. Run structure script via browser
https://yoursite.com/recreate-storage-structure.php

# 5. Generate missing images report
https://yoursite.com/check-missing-images.php
# (Download the CSV for your records)

# 6. Via SSH or cPanel Terminal:
php artisan images:cleanup-missing --reset
php artisan images:notify-missing --send

# 7. Delete security scripts
rm recreate-storage-structure.php
rm check-missing-images.php
```

---

## 💡 Understanding the Commands

### `images:cleanup-missing` Command

**Purpose:** Clean up database references to missing image files

**Options:**
```bash
# Preview mode (safe, no changes)
php artisan images:cleanup-missing --dry-run

# Reset mode (makes changes)
php artisan images:cleanup-missing --reset
```

**What it does:**
- Scans business `logo_path` field
- Scans product `images` JSON array
- Checks if files actually exist on disk
- In `--reset` mode: Sets broken references to NULL

**Output example:**
```
🔍 Checking for missing business logos and product images...

=== BUSINESS LOGOS ===
❌ Missing: Franc's Electrical And Electronics shop -> business/logos/xxx.png
❌ Missing: Superior Trims Barber Shop -> business/logos/yyy.png
Found 5 missing business logos out of 5 total.

=== PRODUCT IMAGES ===
❌ Missing: Product 'Juala' -> products/zzz.png
Found 1 missing product images out of 1 total.

=== SUMMARY ===
+----------------+-------+---------------+--------+
| Type           | Total | Missing Files | Status |
+----------------+-------+---------------+--------+
| Business Logos | 5     | 5             | Reset  |
| Product Images | 1     | 1             | Reset  |
+----------------+-------+---------------+--------+
✅ Missing images have been reset in the database.
```

### `images:notify-missing` Command

**Purpose:** Send email notifications to users with missing logos

**Options:**
```bash
# Preview who will receive emails (safe)
php artisan images:notify-missing

# Actually send emails (production only!)
php artisan images:notify-missing --send

# Send test email
php artisan images:notify-missing --email=your@email.com
```

**What it does:**
- Finds businesses with NULL `logo_path`
- Gets business owner email from `users` table
- Sends professional notification email
- Shows progress bar during send

**Important:** Run `images:cleanup-missing --reset` FIRST, then this command will work properly (it looks for NULL logo_path values).

---

## 📧 Email Notification Details

When you run `php artisan images:notify-missing --send`, affected users receive an email with:

✉️ **Subject:** Action Required: Re-upload Your Business Logo

📄 **Content:**
- Explanation of what happened
- Step-by-step re-upload instructions
- Support contact information
- Professional, reassuring tone

**Email includes:**
- Business name
- Owner name
- Direct link to edit page
- Support email and WhatsApp
- FAQ section

---

## 🔍 How to Check Results

### 1. Database Check (After Reset)
```sql
-- Check businesses with logos
SELECT id, name, logo_path FROM businesses;

-- Businesses with missing logos (should be NULL now)
SELECT COUNT(*) FROM businesses WHERE logo_path IS NULL;

-- Businesses with logos still set
SELECT COUNT(*) FROM businesses WHERE logo_path IS NOT NULL;
```

### 2. File System Check
```bash
# List actual files in storage
ls -la storage/app/public/business/logos/

# Count files
find storage/app/public/business/logos -type f | wc -l
```

### 3. User Experience Check
1. Log in as user with missing logo
2. Go to dashboard
3. See yellow warning banner
4. Click "Upload Logo Now"
5. Upload a test image
6. Verify it displays correctly

---

## 📊 Before & After

### BEFORE (Problem State):
- ❌ Storage folder deleted
- ❌ Database has image paths but files don't exist
- ❌ Users see 403/404 errors
- ❌ Broken images on landing page
- ❌ Console errors: "Failed to load resource: 403"

### AFTER (Fixed State):
- ✅ Storage structure recreated
- ✅ Database references cleaned (NULL for missing)
- ✅ Default placeholder images show
- ✅ No console errors
- ✅ Users receive notification
- ✅ Dashboard banner guides re-upload
- ✅ New uploads work perfectly

---

## 🎓 Educational: Why This Happened

### Why Symlinks Matter
Laravel stores files in `/storage/app/public/` but serves them from `/public/storage/` via a symbolic link:

```
public/storage → ../storage/app/public
```

When you delete the `storage` folder:
1. Physical files are gone forever
2. Database still has references: `business/logos/abc123.jpg`
3. When browser requests `/storage/business/logos/abc123.jpg`...
4. It follows symlink to `/storage/app/public/business/logos/abc123.jpg`
5. File doesn't exist → 403 Forbidden

### Prevention
```bash
# Regular backups
tar -czf storage-backup.tar.gz storage/app/public/

# Automated weekly check
# Add to app/Console/Kernel.php:
$schedule->command('images:cleanup-missing --dry-run')
         ->weekly()
         ->emailOutputTo('admin@yoursite.com');
```

---

## 🆘 Troubleshooting

### Problem: Commands not found
**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan list | grep images
```

### Problem: Emails not sending
**Solution:**
Check `.env` mail configuration:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@yoursite.com
```

Test with:
```bash
php artisan images:notify-missing --email=test@example.com
```

### Problem: Symlink error
**Solution:**
```bash
rm public/storage
php artisan storage:link
```

### Problem: Permission denied
**Solution:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Problem: Dashboard banner not showing
**Solution:**
1. Clear browser cache
2. Check if user's business has `logo_path = NULL`
3. Clear Laravel view cache: `php artisan view:clear`

---

## 📝 Checklist for Recovery

- [ ] Storage structure recreated locally
- [ ] Run `images:cleanup-missing --dry-run` locally
- [ ] Run `images:cleanup-missing --reset` locally
- [ ] Test uploading new logo locally
- [ ] Commit and push changes
- [ ] Deploy to cPanel
- [ ] Run recreate script on cPanel
- [ ] Run cleanup command on cPanel
- [ ] Send email notifications
- [ ] Delete security scripts
- [ ] Verify dashboard banner shows
- [ ] Verify landing page shows default images
- [ ] Monitor user re-uploads

---

## 📞 Support

If you need help:
1. Check `STORAGE-RECOVERY-GUIDE.md` for detailed instructions
2. Check Laravel logs: `storage/logs/laravel.log`
3. Contact hosting support for file permission issues

---

## 🎉 Success Indicators

You'll know it's working when:
- ✅ No 403/404 errors in browser console
- ✅ Default placeholder images display
- ✅ Users can upload new logos successfully
- ✅ Dashboard banner shows for affected users
- ✅ Email notifications delivered

---

**Estimated Recovery Time:** 30-60 minutes  
**Affected Users:** 5 businesses  
**Data Loss:** Image files only (recoverable by users)  
**System Integrity:** 100% intact

Good luck! 🚀

---

**Created:** October 4, 2025  
**Laravel Version:** 10.x  
**PHP Version:** 8.1+

