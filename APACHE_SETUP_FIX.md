# Apache Setup Fix for Shopybook Website Builder

## 🔴 Current Issue
When clicking "Preview" on website pages, you get:
```
Not Found
The requested URL was not found on this server.
Apache/2.4.58 (Win64) Port 80
```

The URLs are being generated correctly as: `http://localhost/website/{subdomain}/{page}`

---

## ✅ Solution Options

### Option 1: Use Laravel Development Server (EASIEST - RECOMMENDED)

1. **Stop Apache** (if running)

2. **Start Laravel's built-in server:**
   ```bash
   php artisan serve
   ```

3. **Update your `.env` file:**
   ```env
   APP_URL=http://127.0.0.1:8000
   ```

4. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

5. **Access your application at:**
   - Dashboard: `http://127.0.0.1:8000`
   - Websites: `http://127.0.0.1:8000/website/{subdomain}`

**✨ Benefits:**
- Works immediately, no configuration needed
- Better for Laravel development
- Hot reload support
- Easy debugging

---

### Option 2: Fix Apache Configuration

If you prefer to continue using Apache:

#### Step 1: Configure Apache Virtual Host

Edit your Apache config file (usually `httpd-vhosts.conf` or `httpd.conf`):

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "C:/Users/harve/Desktop/Shopybook/public"
    
    <Directory "C:/Users/harve/Desktop/Shopybook/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/shopybook-error.log"
    CustomLog "logs/shopybook-access.log" common
</VirtualHost>
```

**⚠️ Important:** Change `DocumentRoot` to point to your `public` folder, NOT the root folder!

#### Step 2: Enable mod_rewrite

Make sure these modules are enabled in `httpd.conf`:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

#### Step 3: Restart Apache

```bash
# In XAMPP Control Panel, stop and start Apache
# OR via command line:
httpd -k restart
```

#### Step 4: Update .env

```env
APP_URL=http://localhost
```

#### Step 5: Clear Laravel cache

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

### Option 3: Quick Test with PHP Built-in Server

Just to test if everything works:

```bash
php -S localhost:8000 -t public
```

Then access: `http://localhost:8000`

---

## 🧪 Testing Your Setup

After choosing a solution:

1. **Access the dashboard:**
   - Laravel server: `http://127.0.0.1:8000/dashboard`
   - Apache: `http://localhost/dashboard`

2. **Go to Website Builder**

3. **Click "Edit" on a page**

4. **Click "Preview"**

5. **It should now work!** ✅

---

## 🔍 Verify Your URLs

Run this test script to see what URLs are being generated:

```bash
php test-url.php
```

Should output:
```
Website URL: http://localhost/website/havis-greenhouse-materials
Page Slug: about
Full URL: http://localhost/website/havis-greenhouse-materials/about
APP_ENV: local
APP_URL: http://localhost
```

---

## 📝 Current Status

✅ Routes are registered correctly
✅ URLs are being generated correctly  
✅ Laravel configuration is correct
❌ Apache DocumentRoot needs to point to `/public` folder

---

## 💡 My Recommendation

**Use Laravel's development server** (`php artisan serve`) because:

1. ✅ No Apache configuration needed
2. ✅ Works out of the box
3. ✅ Better for development
4. ✅ Easier debugging
5. ✅ Can always switch to Apache later for production

Just run:
```bash
php artisan serve
```

Then access: `http://127.0.0.1:8000` 🚀

---

## 🆘 Still Having Issues?

If the problem persists:

1. **Check your DocumentRoot** - Must point to the `public` folder
2. **Check `.htaccess` exists** in `public` folder
3. **Check mod_rewrite is enabled** in Apache
4. **Clear all caches:**
   ```bash
   php artisan optimize:clear
   ```

5. **Check Laravel logs:**
   ```
   storage/logs/laravel.log
   ```

---

**Last Updated:** October 27, 2025


