# Website Builder URL Configuration

## Environment-Aware URL System

The Shopybook website builder automatically adapts its URL structure based on your environment.

---

## 🏠 Local Development

When running on localhost, websites use **path-based URLs**:

```
http://localhost/website/{businessslug}
http://localhost/website/myshop
http://localhost/website/acmecorp
```

**Example Pages:**
- Homepage: `http://localhost/website/myshop`
- About Page: `http://localhost/website/myshop/about`
- Contact: `http://localhost/website/myshop/contact`

---

## 🌐 Production

In production, websites use **subdomain URLs**:

```
https://{businessslug}.shopybook.com
https://myshop.shopybook.com
https://acmecorp.shopybook.com
```

**Example Pages:**
- Homepage: `https://myshop.shopybook.com`
- About Page: `https://myshop.shopybook.com/about`
- Contact: `https://myshop.shopybook.com/contact`

---

## ⚙️ Configuration

The system automatically detects the environment based on:

1. **`APP_ENV`** in `.env` file
   ```env
   APP_ENV=local  # Uses path-based URLs
   APP_ENV=production  # Uses subdomain URLs
   ```

2. **`APP_URL`** in `.env` file
   ```env
   # Local
   APP_URL=http://localhost

   # Production
   APP_URL=https://shopybook.com
   ```

---

## 🔧 How It Works

### Website Model (`app/Models/Website.php`)

The `getUrlAttribute()` method automatically detects the environment:

```php
public function getUrlAttribute(): string
{
    // Custom domain takes precedence
    if ($this->custom_domain) {
        return "https://{$this->custom_domain}";
    }
    
    // Local: http://localhost/website/{subdomain}
    if ($this->isLocalEnvironment()) {
        return $this->getLocalUrl();
    }
    
    // Production: https://{subdomain}.shopybook.com
    return "https://{$this->subdomain}.shopybook.com";
}
```

### Routes (`routes/website.php`)

Routes are conditionally registered based on environment:

```php
if ($isLocal) {
    // Path-based: /website/{subdomain}
    Route::prefix('website/{subdomain}')->group(function () {
        // Routes...
    });
} else {
    // Subdomain: {subdomain}.shopybook.com
    Route::domain('{subdomain}.shopybook.com')->group(function () {
        // Routes...
    });
}
```

---

## 🎯 Testing

### Test in Local Environment

1. Create a website in the builder
2. Access it at: `http://localhost/website/{your-business-slug}`
3. Example: `http://localhost/website/myshop`

### Test in Production

1. Deploy to production server
2. Configure DNS for `*.shopybook.com` to point to your server
3. Access at: `https://{business-slug}.shopybook.com`

---

## 🔐 Custom Domains (Future)

Businesses can optionally use their own custom domains:

```
https://www.mybusiness.com  (instead of mybusiness.shopybook.com)
```

This is configured via the `custom_domain` field in the `websites` table.

---

## 📝 Important Notes

1. **Clear Cache**: After changing environment, clear cache:
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan cache:clear
   ```

2. **DNS Setup (Production)**: Configure wildcard subdomain:
   ```
   *.shopybook.com  →  Your Server IP
   ```

3. **SSL (Production)**: Use wildcard SSL certificate:
   ```
   *.shopybook.com
   ```

4. **No Code Changes**: The system automatically adapts. No code changes needed when moving between environments.

---

## 🚀 Deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_URL=https://shopybook.com` in `.env`
- [ ] Configure wildcard DNS (`*.shopybook.com`)
- [ ] Install wildcard SSL certificate
- [ ] Clear all caches
- [ ] Test with a sample website

---

## 💡 Tips

- URLs are generated automatically via `$website->url`
- The system works seamlessly across all views and controllers
- No manual URL construction needed
- Preview links in the dashboard automatically use the correct format

---

**Last Updated:** October 2025


