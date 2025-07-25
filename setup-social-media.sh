#!/bin/bash

# Shopybook Social Media Setup Script
# This script helps you quickly test the social media integration

echo "🚀 Shopybook Social Media Integration Setup"
echo "==========================================="

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ .env file not found. Please copy .env.example to .env first."
    exit 1
fi

echo "✅ .env file found"

# Check Laravel setup
echo "🔧 Checking Laravel setup..."

# Clear config cache
php artisan config:clear
echo "✅ Config cache cleared"

# Run migrations
php artisan migrate --force
echo "✅ Database migrations completed"

# Check if social media tables exist
echo "📊 Checking social media tables..."
php artisan tinker --execute="
try {
    \App\Models\SocialMediaAccount::count();
    echo '✅ SocialMediaAccount model working\n';
} catch (Exception \$e) {
    echo '❌ SocialMediaAccount model error: ' . \$e->getMessage() . '\n';
}

try {
    \App\Models\MarketingPost::count();
    echo '✅ MarketingPost model working\n';
} catch (Exception \$e) {
    echo '❌ MarketingPost model error: ' . \$e->getMessage() . '\n';
}

try {
    \App\Models\PostPublication::count();
    echo '✅ PostPublication model working\n';
} catch (Exception \$e) {
    echo '❌ PostPublication model error: ' . \$e->getMessage() . '\n';
}
"

# Test social media service
echo "🧪 Testing Social Media Service..."
php artisan tinker --execute="
try {
    \$service = new \App\Services\SocialMediaService();
    \$suggestions = \$service->generateContentSuggestions('retail');
    echo '✅ SocialMediaService working - Generated ' . count(\$suggestions) . ' suggestions\n';
} catch (Exception \$e) {
    echo '❌ SocialMediaService error: ' . \$e->getMessage() . '\n';
}
"

# Check environment variables for social media
echo "🔑 Checking API credentials in .env..."

check_env_var() {
    local var_name=$1
    local var_value=$(grep "^$var_name=" .env | cut -d '=' -f2-)
    
    if [ -z "$var_value" ] || [ "$var_value" = "your_placeholder_value" ]; then
        echo "⚠️  $var_name not configured"
        return 1
    else
        echo "✅ $var_name configured"
        return 0
    fi
}

echo ""
echo "Free Platform Credentials:"
check_env_var "TELEGRAM_BOT_TOKEN"
check_env_var "DISCORD_CLIENT_ID"
check_env_var "REDDIT_CLIENT_ID"

echo ""
echo "Paid Platform Credentials:"
check_env_var "FACEBOOK_CLIENT_ID"
check_env_var "TWITTER_CLIENT_ID"
check_env_var "LINKEDIN_CLIENT_ID"
check_env_var "YOUTUBE_CLIENT_ID"

echo ""
echo "🌐 Testing routes..."

# Check if routes are registered
php artisan route:list --grep=social

echo ""
echo "📱 Next Steps:"
echo "==============="
echo ""
echo "1. 🆓 START WITH FREE PLATFORMS:"
echo "   - Set up Telegram bot: https://t.me/BotFather"
echo "   - Create Discord app: https://discord.com/developers/applications"
echo "   - Register Reddit app: https://www.reddit.com/prefs/apps"
echo ""
echo "2. 🔧 CONFIGURE YOUR .ENV:"
echo "   - Add your bot tokens and API keys"
echo "   - Update APP_URL to your domain"
echo ""
echo "3. 🧪 TEST THE INTEGRATION:"
echo "   - Visit: http://localhost:8000/marketing/social-media"
echo "   - Connect a free platform first"
echo "   - Try creating and publishing a test post"
echo ""
echo "4. 💼 UPGRADE TO BUSINESS PLATFORMS:"
echo "   - Facebook/Instagram (requires business verification)"
echo "   - Twitter (requires $100/month plan)"
echo "   - LinkedIn (requires company page)"
echo ""
echo "📖 For detailed setup instructions, see:"
echo "   SOCIAL_MEDIA_API_SETUP_GUIDE.md"
echo ""
echo "🎉 Setup complete! Happy posting!"
