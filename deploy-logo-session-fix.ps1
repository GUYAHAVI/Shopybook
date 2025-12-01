# ============================================
# Logo Generation Session Fix - Deploy Script
# ============================================
# Run this script after pulling changes to production

Write-Host "🚀 Deploying Logo Generation Session Fix..." -ForegroundColor Cyan
Write-Host ""

# Step 1: Commit changes locally
Write-Host "📦 Step 1: Committing changes..." -ForegroundColor Yellow
git add resources/views/dashboard.blade.php resources/views/business/edit.blade.php resources/views/business/create.blade.php LOGO_SESSION_FIX.md
git commit -m "Fix logo generation authentication with enhanced AJAX headers and redirect detection"

# Step 2: Push to remote
Write-Host ""
Write-Host "⬆️  Step 2: Pushing to GitHub..." -ForegroundColor Yellow
git push origin main

Write-Host ""
Write-Host "✅ Changes pushed to GitHub!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps (Run on cPanel/Production Server):" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. SSH into your server or use cPanel Terminal" -ForegroundColor White
Write-Host ""
Write-Host "2. Navigate to project directory:" -ForegroundColor White
Write-Host "   cd /home1/shopyboo/public_html" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Pull latest changes:" -ForegroundColor White
Write-Host "   git pull origin main" -ForegroundColor Gray
Write-Host ""
Write-Host "4. Clear all Laravel caches:" -ForegroundColor White
Write-Host "   php artisan cache:clear" -ForegroundColor Gray
Write-Host "   php artisan config:clear" -ForegroundColor Gray
Write-Host "   php artisan route:clear" -ForegroundColor Gray
Write-Host "   php artisan view:clear" -ForegroundColor Gray
Write-Host ""
Write-Host "5. Regenerate optimized files:" -ForegroundColor White
Write-Host "   php artisan config:cache" -ForegroundColor Gray
Write-Host "   php artisan route:cache" -ForegroundColor Gray
Write-Host "   php artisan view:cache" -ForegroundColor Gray
Write-Host ""
Write-Host "6. Test logo generation:" -ForegroundColor White
Write-Host "   - Open https://shopybook.com" -ForegroundColor Gray
Write-Host "   - Press F12 to open Console" -ForegroundColor Gray
Write-Host "   - Click 'Generate Logo' button" -ForegroundColor Gray
Write-Host "   - Watch console for success message" -ForegroundColor Gray
Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📖 Expected Console Output (Success):" -ForegroundColor Green
Write-Host "✓ JSON parsed successfully: {success: true, logo_url: '...', ...}" -ForegroundColor Gray
Write-Host ""
Write-Host "📖 If Session Expired (Need to refresh):" -ForegroundColor Yellow
Write-Host "❌ REQUEST REDIRECTED TO: https://shopybook.com/login" -ForegroundColor Gray
Write-Host "Error: Your session has expired. Please refresh the page and log in again." -ForegroundColor Gray
Write-Host ""
Write-Host "📚 Full documentation: LOGO_SESSION_FIX.md" -ForegroundColor Cyan
Write-Host ""
