# Shopybook Social Media Setup Script (PowerShell)
# This script helps you quickly test the social media integration

Write-Host "🚀 Shopybook Social Media Integration Setup" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Green

# Check if .env file exists
if (-not (Test-Path .env)) {
    Write-Host "❌ .env file not found. Please copy .env.example to .env first." -ForegroundColor Red
    exit 1
}

Write-Host "✅ .env file found" -ForegroundColor Green

# Check Laravel setup
Write-Host "🔧 Checking Laravel setup..." -ForegroundColor Yellow

# Clear config cache
php artisan config:clear
Write-Host "✅ Config cache cleared" -ForegroundColor Green

# Run migrations
php artisan migrate --force
Write-Host "✅ Database migrations completed" -ForegroundColor Green

# Check if social media tables exist
Write-Host "📊 Checking social media tables..." -ForegroundColor Yellow

$thinkerScript = @"
try {
    \App\Models\SocialMediaAccount::count();
    echo "✅ SocialMediaAccount model working\n";
} catch (Exception `$e) {
    echo "❌ SocialMediaAccount model error: " . `$e->getMessage() . "\n";
}

try {
    \App\Models\MarketingPost::count();
    echo "✅ MarketingPost model working\n";
} catch (Exception `$e) {
    echo "❌ MarketingPost model error: " . `$e->getMessage() . "\n";
}

try {
    \App\Models\PostPublication::count();
    echo "✅ PostPublication model working\n";
} catch (Exception `$e) {
    echo "❌ PostPublication model error: " . `$e->getMessage() . "\n";
}
"@

php artisan tinker --execute="$thinkerScript"

# Test social media service
Write-Host "🧪 Testing Social Media Service..." -ForegroundColor Yellow

$serviceTest = @"
try {
    `$service = new \App\Services\SocialMediaService();
    `$suggestions = `$service->generateContentSuggestions('retail');
    echo "✅ SocialMediaService working - Generated " . count(`$suggestions) . " suggestions\n";
} catch (Exception `$e) {
    echo "❌ SocialMediaService error: " . `$e->getMessage() . "\n";
}
"@

php artisan tinker --execute="$serviceTest"

# Check environment variables for social media
Write-Host "🔑 Checking API credentials in .env..." -ForegroundColor Yellow

function Check-EnvVar {
    param($varName)
    
    $envContent = Get-Content .env -ErrorAction SilentlyContinue
    $varLine = $envContent | Where-Object { $_ -match "^$varName=" }
    
    if ($varLine) {
        $varValue = ($varLine -split '=', 2)[1]
        if ($varValue -and $varValue -ne "your_placeholder_value" -and $varValue.Trim() -ne "") {
            Write-Host "✅ $varName configured" -ForegroundColor Green
            return $true
        }
    }
    
    Write-Host "⚠️  $varName not configured" -ForegroundColor Yellow
    return $false
}

Write-Host ""
Write-Host "Free Platform Credentials:" -ForegroundColor Cyan
Check-EnvVar "TELEGRAM_BOT_TOKEN"
Check-EnvVar "DISCORD_CLIENT_ID"
Check-EnvVar "REDDIT_CLIENT_ID"

Write-Host ""
Write-Host "Paid Platform Credentials:" -ForegroundColor Cyan
Check-EnvVar "FACEBOOK_CLIENT_ID"
Check-EnvVar "TWITTER_CLIENT_ID"
Check-EnvVar "LINKEDIN_CLIENT_ID"
Check-EnvVar "YOUTUBE_CLIENT_ID"

Write-Host ""
Write-Host "🌐 Testing routes..." -ForegroundColor Yellow

# Check if routes are registered
php artisan route:list --grep=social

Write-Host ""
Write-Host "📱 Next Steps:" -ForegroundColor Green
Write-Host "===============" -ForegroundColor Green
Write-Host ""
Write-Host "1. 🆓 START WITH FREE PLATFORMS:" -ForegroundColor Cyan
Write-Host "   - Set up Telegram bot: https://t.me/BotFather"
Write-Host "   - Create Discord app: https://discord.com/developers/applications"
Write-Host "   - Register Reddit app: https://www.reddit.com/prefs/apps"
Write-Host ""
Write-Host "2. 🔧 CONFIGURE YOUR .ENV:" -ForegroundColor Cyan
Write-Host "   - Add your bot tokens and API keys"
Write-Host "   - Update APP_URL to your domain"
Write-Host ""
Write-Host "3. 🧪 TEST THE INTEGRATION:" -ForegroundColor Cyan
Write-Host "   - Visit: http://localhost:8000/marketing/social-media"
Write-Host "   - Connect a free platform first"
Write-Host "   - Try creating and publishing a test post"
Write-Host ""
Write-Host "4. 💼 UPGRADE TO BUSINESS PLATFORMS:" -ForegroundColor Cyan
Write-Host "   - Facebook/Instagram (requires business verification)"
Write-Host "   - Twitter (requires `$100/month plan)"
Write-Host "   - LinkedIn (requires company page)"
Write-Host ""
Write-Host "📖 For detailed setup instructions, see:" -ForegroundColor Yellow
Write-Host "   SOCIAL_MEDIA_API_SETUP_GUIDE.md"
Write-Host ""
Write-Host "🎉 Setup complete! Happy posting!" -ForegroundColor Green
