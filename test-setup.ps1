# Social Media Setup Test Script for Laravel
Write-Host "Starting Social Media Setup Test..." -ForegroundColor Green

# Clear Laravel caches
Write-Host "Clearing Laravel caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Test configuration
Write-Host "Testing configuration..." -ForegroundColor Yellow
php artisan config:cache

# Check if social media routes are registered
Write-Host "Checking social media routes..." -ForegroundColor Yellow
php artisan route:list --name=social

# Test database connection
Write-Host "Testing database connection..." -ForegroundColor Yellow
php artisan migrate:status

# Check if services are properly configured
Write-Host "Testing services configuration..." -ForegroundColor Yellow
php -r "
try {
    require 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$config = \$app->make('config');
    echo 'Services config loaded successfully' . PHP_EOL;
    
    // Check if social media services are configured
    \$services = \$config->get('services');
    \$socialPlatforms = ['facebook', 'instagram', 'twitter', 'linkedin', 'tiktok', 'youtube'];
    foreach (\$socialPlatforms as \$platform) {
        if (isset(\$services[\$platform])) {
            echo 'Platform configured: ' . \$platform . PHP_EOL;
        }
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

Write-Host "Setup test completed!" -ForegroundColor Green
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Set up API keys for free platforms (Telegram, Discord, Reddit)" -ForegroundColor White
Write-Host "2. Test OAuth connections" -ForegroundColor White
Write-Host "3. Create your first social media post" -ForegroundColor White
