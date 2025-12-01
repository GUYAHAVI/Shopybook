# DNS Configuration Guide for Website Subdomains

## The Problem
When accessing `jihami.shopybook.com`, you get "DNS_PROBE_POSSIBLE" error because:
1. The DNS doesn't have a record for the subdomain
2. Your server isn't configured to handle wildcard subdomains

## Solution Options

### Option 1: Add Wildcard DNS Record (RECOMMENDED for Production)

#### In cPanel DNS Manager:
1. Login to cPanel
2. Go to **Zone Editor** or **Advanced DNS Zone Editor**
3. Add new **A Record**:
   ```
   Name: *
   Type: A
   Address: YOUR_SERVER_IP (same as shopybook.com)
   TTL: 14400
   ```

#### Or add specific subdomain:
```
Name: jihami
Type: A  
Address: YOUR_SERVER_IP
TTL: 14400
```

**Note**: DNS propagation can take 5-60 minutes.

---

### Option 2: Use Direct URL Route (IMMEDIATE - No DNS needed)

Add this route to serve websites without subdomain DNS:

**File**: `routes/web.php` (add near the top)

```php
// Public website viewer (no subdomain needed)
Route::get('/site/{subdomain}', [PublicWebsiteController::class, 'homepage'])
    ->name('public.website.view');

Route::get('/site/{subdomain}/{slug}', [PublicWebsiteController::class, 'page'])
    ->name('public.website.page');
```

**Then access via**:
```
https://shopybook.com/site/jihami
```

---

### Option 3: Configure Web Server for Wildcard Subdomains

#### Apache (in VirtualHost):
```apache
<VirtualHost *:80>
    ServerName shopybook.com
    ServerAlias *.shopybook.com
    DocumentRoot /home/username/public_html/Shopybook/public

    <Directory /home/username/public_html/Shopybook/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:443>
    ServerName shopybook.com
    ServerAlias *.shopybook.com
    DocumentRoot /home/username/public_html/Shopybook/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.crt
    SSLCertificateKeyFile /path/to/private.key
    
    <Directory /home/username/public_html/Shopybook/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## Quick Test Commands

### Check DNS Resolution:
```bash
# On Windows PowerShell:
nslookup jihami.shopybook.com

# Should return your server IP
```

### Check Current Routes:
```bash
php artisan route:list | grep subdomain
```

### Test Subdomain Locally (hosts file):
```
# Windows: C:\Windows\System32\drivers\etc\hosts
# Add this line:
YOUR_SERVER_IP jihami.shopybook.com

# Then access: http://jihami.shopybook.com
```

---

## What to Do Right Now

### IMMEDIATE (5 minutes):
1. **Add the direct URL route** (Option 2 above)
2. Access website via: `https://shopybook.com/site/jihami`
3. This works instantly without DNS changes

### LONG-TERM (Production):
1. **Add wildcard DNS** in cPanel (Option 1)
2. Wait 5-60 minutes for DNS propagation
3. Verify with `nslookup jihami.shopybook.com`
4. Access via: `https://jihami.shopybook.com`

---

## Current Status
✅ Laravel routes are configured for subdomains
✅ Website data exists in database
❌ DNS record missing for subdomain
❌ Web server may need wildcard configuration

## Need Help?
Contact your hosting provider to:
- Add wildcard DNS record (*.shopybook.com)
- Configure Apache/Nginx for subdomain handling
- Enable wildcard SSL certificate
