# 📦 Storage Recovery Guide - After Deleting Storage Folder

## 🚨 Situation
The `storage` folder was deleted, causing all uploaded business logos and product images to be lost. The database still references these files, resulting in 403/404 errors.

## ✅ What You've Done Already
- ✓ Recreated storage directory structure
- ✓ Created storage symlink
- ✓ Identified missing images

---

## 🔄 Recovery Steps

### Step 1: Review Missing Images (COMPLETED ✓)
```bash
php artisan images:cleanup-missing --dry-run
```

**Results from your system:**
- **5 missing business logos**
- **1 missing product image**

### Step 2: Clean Database References
This will set all missing image references to `NULL` in the database, so users see default placeholder images instead of broken links:

```bash
php artisan images:cleanup-missing --reset
```

This will:
- Set `logo_path = NULL` for businesses with missing logos
- Remove broken image paths from products' image arrays
- Users will see default "Upload your logo" placeholders

### Step 3: Deploy to cPanel

#### On cPanel:
1. **Upload Files:**
   - `recreate-storage-structure.php` (to project root)
   - `check-missing-images.php` (to project root)
   - Updated Laravel code

2. **Run Structure Script:**
   ```
   https://yoursite.com/recreate-storage-structure.php
   ```
   This recreates the storage folder structure.

3. **Check Missing Images:**
   ```
   https://yoursite.com/check-missing-images.php
   ```
   This generates a detailed HTML/CSV report.

4. **Run Cleanup (via SSH or Artisan Tinker):**
   ```bash
   php artisan images:cleanup-missing --reset
   ```

5. **Delete Security Scripts:**
   - Delete `recreate-storage-structure.php`
   - Delete `check-missing-images.php`

### Step 4: Notify Affected Users

**Affected Businesses (from your database):**
1. Franc's Electrical And Electronics shop
2. Superior Trims Barber Shop
3. Helcrafters Technologies
4. Cheese
5. Test Services

**Notification Template:**

```
Subject: Action Required: Please Re-upload Your Business Logo

Dear [Business Owner],

We recently performed system maintenance that affected uploaded images. 
Your business logo needs to be re-uploaded.

What to do:
1. Log in to your dashboard
2. Go to Business Settings → Edit Business
3. Upload your logo again

We apologize for the inconvenience. If you need assistance, 
please contact support.

Best regards,
Shopybook Team
```

---

## 🛠️ Automated User Notification System

### Option A: Dashboard Banner (Recommended)

Add this to your dashboard blade:

```php
{{-- resources/views/dashboard.blade.php --}}
@if(Auth::user()->business && empty(Auth::user()->business->logo_path))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5><i class="fas fa-exclamation-triangle"></i> Missing Business Logo</h5>
        <p>Your business logo needs to be uploaded. 
           <a href="{{ route('business.edit') }}" class="alert-link">Click here to upload it now</a>
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

### Option B: Email Notification Command

Create a command to email all affected users:

```bash
php artisan make:command NotifyMissingImages
```

---

## 📊 Database Cleanup Query (Manual Alternative)

If you prefer SQL instead of artisan command:

```sql
-- Check missing logos
SELECT id, name, email, phone, logo_path 
FROM businesses 
WHERE logo_path IS NOT NULL 
  AND logo_path != '';

-- Reset missing business logos to NULL
UPDATE businesses 
SET logo_path = NULL 
WHERE logo_path IS NOT NULL 
  AND logo_path != '';

-- Check products with images
SELECT id, name, images 
FROM products 
WHERE images IS NOT NULL 
  AND images != '[]' 
  AND images != '';

-- Clear product images (you'll need to do this carefully per product)
-- UPDATE products SET images = '[]' WHERE id = ?;
```

---

## 🔐 Prevent Future Data Loss

### 1. Backup Strategy
Create `.cpanel-backup-exclude` file:
```
# Don't exclude storage folder!
# storage/app/public should be backed up
```

### 2. Git Protection
Update `.gitignore`:
```
# Keep directory structure but not contents
storage/app/public/*
!storage/app/public/.gitignore
!storage/app/public/business/
!storage/app/public/business/.gitignore
!storage/app/public/products/
!storage/app/public/products/.gitignore
```

### 3. Regular Backups
```bash
# Backup storage folder
tar -czf storage-backup-$(date +%Y%m%d).tar.gz storage/app/public/

# Upload to offsite location
```

### 4. Storage Guard Middleware

Create a scheduled check:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('images:cleanup-missing --dry-run')
             ->weekly()
             ->emailOutputTo('admin@yoursite.com');
}
```

---

## 🎯 Quick Checklist

- [ ] Run `php artisan images:cleanup-missing --dry-run` (locally)
- [ ] Run `php artisan images:cleanup-missing --reset` (locally)
- [ ] Test uploading new logo (locally)
- [ ] Commit and push code changes
- [ ] Deploy to cPanel
- [ ] Run `recreate-storage-structure.php` on cPanel
- [ ] Run `check-missing-images.php` and download report
- [ ] Run cleanup command on cPanel (SSH or Tinker)
- [ ] Delete security scripts from cPanel
- [ ] Send email notifications to affected users
- [ ] Add dashboard banner for missing logos
- [ ] Set up automated backups
- [ ] Monitor for user re-uploads

---

## 📞 Support for Affected Users

### FAQ for Users

**Q: Why is my logo gone?**  
A: Due to a system maintenance issue, uploaded images need to be re-uploaded. We apologize for the inconvenience.

**Q: Do I need to pay again?**  
A: No, this is a free service issue recovery. Your account and data are intact.

**Q: What file formats are supported?**  
A: JPG, PNG, GIF, SVG (Max size: 2MB recommended)

**Q: Can you recover my old logo?**  
A: Unfortunately, the files were permanently deleted and cannot be recovered. Please re-upload from your original source.

---

## 🔧 Troubleshooting

### Storage Link Issues
```bash
# Remove old symlink
rm public/storage

# Recreate
php artisan storage:link
```

### Permission Issues (cPanel)
```bash
chmod -R 755 storage/
chmod -R 755 public/storage/
```

### Images Still Not Showing
1. Check browser console for errors
2. Verify storage link exists: `ls -la public/storage`
3. Check file ownership: `ls -la storage/app/public/business/logos`
4. Clear Laravel cache: `php artisan cache:clear`
5. Clear browser cache

---

## 📈 Monitoring Recovery

Track user compliance:

```sql
-- Users who've re-uploaded
SELECT COUNT(*) as 'Re-uploaded' 
FROM businesses 
WHERE logo_path IS NOT NULL;

-- Users still needing to upload
SELECT id, name, email, phone
FROM businesses 
WHERE logo_path IS NULL 
  AND active = 1
ORDER BY created_at DESC;
```

---

**Recovery Time Estimate:** 1-2 hours  
**User Impact:** 5 businesses need to re-upload logos  
**Action Priority:** HIGH (affects user experience)

**Last Updated:** October 4, 2025

