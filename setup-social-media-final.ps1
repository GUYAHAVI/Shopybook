#!/usr/bin/env powershell

# Shopybook Social Media Setup Script
# This script helps you get started with FREE social media platforms

Write-Host "🚀 Shopybook Social Media Setup" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host ""

# Check if .env exists
if (!(Test-Path ".env")) {
    Write-Host "❌ .env file not found!" -ForegroundColor Red
    Write-Host "   Please copy .env.example to .env first:" -ForegroundColor Yellow
    Write-Host "   cp .env.example .env" -ForegroundColor Cyan
    exit 1
}

Write-Host "✅ .env file found" -ForegroundColor Green

# Clear Laravel caches
Write-Host "🧹 Clearing Laravel caches..." -ForegroundColor Blue
try {
    php artisan config:clear | Out-Null
    php artisan cache:clear | Out-Null
    php artisan route:clear | Out-Null
    Write-Host "✅ Caches cleared successfully" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Warning: Could not clear all caches" -ForegroundColor Yellow
}

# Check routes
Write-Host "🔍 Checking social media routes..." -ForegroundColor Blue
$routes = php artisan route:list --name=social 2>$null
if ($routes) {
    Write-Host "✅ Social media routes registered" -ForegroundColor Green
} else {
    Write-Host "⚠️  Warning: Social media routes not found" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "📋 Setup Checklist:" -ForegroundColor Cyan
Write-Host "==================" -ForegroundColor Cyan
Write-Host ""

# Check for social media tokens in .env
$envContent = Get-Content ".env" -ErrorAction SilentlyContinue
if ($envContent) {
    $telegramToken = $envContent | Select-String "TELEGRAM_BOT_TOKEN=" | Select-Object -First 1
    $discordToken = $envContent | Select-String "DISCORD_BOT_TOKEN=" | Select-Object -First 1
    $redditClient = $envContent | Select-String "REDDIT_CLIENT_ID=" | Select-Object -First 1
    
    if ($telegramToken -and $telegramToken -notmatch "your_.*_here") {
        Write-Host "✅ Telegram Bot Token configured" -ForegroundColor Green
    } else {
        Write-Host "❌ Telegram Bot Token not configured" -ForegroundColor Red
        Write-Host "   📖 See: QUICK_START_FREE_PLATFORMS.md - Section 1" -ForegroundColor Yellow
    }
    
    if ($discordToken -and $discordToken -notmatch "your_.*_here") {
        Write-Host "✅ Discord Bot Token configured" -ForegroundColor Green
    } else {
        Write-Host "❌ Discord Bot Token not configured" -ForegroundColor Red
        Write-Host "   📖 See: QUICK_START_FREE_PLATFORMS.md - Section 2" -ForegroundColor Yellow
    }
    
    if ($redditClient -and $redditClient -notmatch "your_.*_here") {
        Write-Host "✅ Reddit API configured" -ForegroundColor Green
    } else {
        Write-Host "❌ Reddit API not configured" -ForegroundColor Red
        Write-Host "   📖 See: QUICK_START_FREE_PLATFORMS.md - Section 3" -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ Could not read .env file" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎯 Next Steps:" -ForegroundColor Magenta
Write-Host "==============" -ForegroundColor Magenta
Write-Host ""

Write-Host "1. 📖 Read the quick start guide:" -ForegroundColor White
Write-Host "   QUICK_START_FREE_PLATFORMS.md" -ForegroundColor Cyan
Write-Host ""

Write-Host "2. 🔧 Configure your API keys:" -ForegroundColor White
Write-Host "   Use .env.social-media.example as a template" -ForegroundColor Cyan
Write-Host ""

Write-Host "3. 🚀 Start Laravel server:" -ForegroundColor White
Write-Host "   php artisan serve" -ForegroundColor Cyan
Write-Host ""

Write-Host "4. 🌐 Visit your social media dashboard:" -ForegroundColor White
Write-Host "   http://localhost:8000/marketing/social-media" -ForegroundColor Cyan
Write-Host ""

Write-Host "📚 Available Documentation:" -ForegroundColor Yellow
Write-Host "- QUICK_START_FREE_PLATFORMS.md (Start here!)"
Write-Host "- SOCIAL_MEDIA_API_SETUP_GUIDE.md (Complete guide)"
Write-Host "- .env.social-media.example (Environment template)"
Write-Host ""

Write-Host "💡 Pro Tip: Start with Telegram - it's the easiest!" -ForegroundColor Green
Write-Host "   Setup time: ~3 minutes" -ForegroundColor Green
Write-Host ""

Write-Host "🆘 Need help? Check the troubleshooting section in:" -ForegroundColor Blue
Write-Host "   QUICK_START_FREE_PLATFORMS.md" -ForegroundColor Cyan
