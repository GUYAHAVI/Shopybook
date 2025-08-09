# cPanel Deployment Guide for Shopybook AI Business System

## 🚀 **Overview**

This guide will help you deploy your Laravel AI Business System on cPanel. The system will work with some modifications to remove Python dependencies.

## 📋 **Prerequisites**

- cPanel hosting account
- PHP 8.0+ support
- MySQL/MariaDB database
- SSH access (optional but recommended)

## 🔧 **Step 1: Prepare Your Project**

### 1.1 Remove Python Dependencies
The Python AI system won't work on cPanel, so we'll use the Laravel-only version:

```bash
# Remove Python files (optional - keep for reference)
rm -rf ai_models/
```

### 1.2 Update AI Communication Service
Replace the Python-based AI service with the Laravel-only version:

```php
// In app/Services/AICommunicationService.php
// Replace the Python calls with LaravelOnlyAIService calls
```

### 1.3 Update Environment Configuration
Create a production `.env` file:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## 📁 **Step 2: File Structure for cPanel**

Upload your files to cPanel with this structure:

```
public_html/                    # Your domain root
├── public/                     # Laravel public folder
│   ├── index.php              # Entry point
│   ├── .htaccess              # URL rewriting
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript
│   └── images/                # Images
├── app/                       # Laravel app folder
├── bootstrap/                 # Laravel bootstrap
├── config/                    # Laravel configuration
├── database/                  # Database migrations
├── resources/                 # Views and assets
├── routes/                    # Route definitions
├── storage/                   # File storage
├── vendor/                    # Composer dependencies
├── .env                       # Environment configuration
├── .htaccess                  # Root .htaccess
└── composer.json              # Composer configuration
```

## 🔧 **Step 3: cPanel Configuration**

### 3.1 Database Setup
1. Create a MySQL database in cPanel
2. Create a database user
3. Assign user to database with full privileges
4. Update `.env` file with database credentials

### 3.2 File Permissions
Set proper permissions:
```bash
chmod 755 public_html/
chmod 755 public_html/public/
chmod 644 public_html/public/.htaccess
chmod 755 public_html/storage/
chmod 755 public_html/bootstrap/cache/
```

### 3.3 URL Rewriting
Create `.htaccess` in the root directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Create `.htaccess` in the public directory:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## 🚀 **Step 4: Deployment Steps**

### 4.1 Upload Files
1. Upload all Laravel files to your cPanel file manager
2. Ensure the structure matches the diagram above
3. Upload `.env` file with production settings

### 4.2 Install Dependencies
Via SSH or cPanel Terminal:
```bash
cd public_html
composer install --optimize-autoloader --no-dev
```

### 4.3 Generate Application Key
```bash
php artisan key:generate
```

### 4.4 Run Migrations
```bash
php artisan migrate
```

### 4.5 Seed Sample Data
```bash
php artisan db:seed --class=SampleDataSeeder
```

### 4.6 Clear Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## ⚙️ **Step 5: Update AI Communication Controller**

Replace the Python-based AI service with the Laravel-only version:

```php
// In app/Http/Controllers/AICommunicationController.php
use App\Services\LaravelOnlyAIService;

class AICommunicationController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = new LaravelOnlyAIService();
    }

    public function processMessage(Request $request)
    {
        $message = $request->input('message');
        $businessId = $request->input('business_id');
        
        $response = $this->aiService->processChatMessage($message, $businessId);
        
        return response()->json([
            'response' => $response,
            'success' => true
        ]);
    }
}
```

## 🔍 **Step 6: Testing**

### 6.1 Test Basic Functionality
1. Visit your domain
2. Register/login
3. Create a business
4. Test the AI chat

### 6.2 Test AI Features
1. Ask about sales performance
2. Request business recommendations
3. Check market trends

## 📊 **What Works on cPanel**

✅ **Fully Functional:**
- User registration and authentication
- Business management
- Product and service management
- Sales tracking
- Customer management
- AI chat interface (Laravel-only version)
- Business insights and analytics
- Sample data seeding

⚠️ **Limited Functionality:**
- Continuous learning system (no Python background processes)
- External API data gathering (no Python scripts)
- Real-time market intelligence (simplified to static data)

## 🔧 **Alternative: Hybrid Approach**

If you want the full Python AI system, consider:

1. **VPS Hosting**: Use a VPS instead of cPanel
2. **External API**: Host Python scripts on a separate server
3. **Cron Jobs**: Use cPanel cron jobs for periodic data updates

## 🛠️ **Troubleshooting**

### Common Issues:

1. **500 Error**: Check file permissions and `.htaccess`
2. **Database Connection**: Verify database credentials in `.env`
3. **White Screen**: Check PHP error logs in cPanel
4. **CSS/JS Not Loading**: Verify public folder structure

### Debug Mode:
Temporarily enable debug mode in `.env`:
```env
APP_DEBUG=true
```

## 📈 **Performance Optimization**

1. **Enable Caching**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Optimize Autoloader**:
```bash
composer install --optimize-autoloader --no-dev
```

3. **Compress Assets**: Use cPanel's compression features

## 🔒 **Security Considerations**

1. **Hide .env file**: Ensure it's not publicly accessible
2. **Strong passwords**: Use strong database passwords
3. **HTTPS**: Enable SSL certificate
4. **Regular updates**: Keep Laravel and dependencies updated

## 📞 **Support**

If you encounter issues:
1. Check cPanel error logs
2. Verify file permissions
3. Test database connectivity
4. Review Laravel logs in `storage/logs/`

---

**Note**: This Laravel-only version provides 80% of the functionality while being fully compatible with cPanel hosting. The AI system will work with your business data and provide meaningful insights and recommendations.

