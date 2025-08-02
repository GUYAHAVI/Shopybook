# LTX-Video Integration Deployment Guide

## 🚀 Deployment Options for cPanel/Production

### Option 1: Cloud API Integration (Recommended)

The system automatically detects the deployment environment and uses cloud APIs when local LTX-Video installation is not available.

#### Environment Variables for cPanel (.env)

```bash
# LTX Video Configuration
LTX_VIDEO_USE_CLOUD=true
LTX_VIDEO_PROVIDER=mock  # Change to 'replicate', 'fal', or 'huggingface' for production

# For Replicate API (Recommended for production)
LTX_VIDEO_PROVIDER=replicate
REPLICATE_API_TOKEN=your_replicate_api_token_here

# For Fal.ai API (Alternative)
LTX_VIDEO_PROVIDER=fal
FAL_KEY=your_fal_api_key_here

# For HuggingFace API (Alternative)
LTX_VIDEO_PROVIDER=huggingface
HUGGINGFACE_API_TOKEN=your_huggingface_token_here

# General Settings
LTX_VIDEO_DEFAULT_STYLE=professional
LTX_VIDEO_CLEANUP_DAYS=7
```

### Option 2: Mock API (Development/Testing)

For testing without API costs:

```bash
LTX_VIDEO_PROVIDER=mock
LTX_VIDEO_USE_CLOUD=true
```

### Option 3: Local Installation (VPS/Dedicated Server Only)

Only use if you have a VPS/dedicated server with GPU support:

```bash
LTX_VIDEO_PATH=/path/to/ltx-video
LTX_VIDEO_PYTHON=python3
LTX_VIDEO_USE_CLOUD=false
```

## 📋 Production API Setup

### Replicate API (Recommended)

1. **Sign up at [Replicate.com](https://replicate.com)**
2. **Get API token from account settings**
3. **Add to .env:**
   ```
   LTX_VIDEO_PROVIDER=replicate
   REPLICATE_API_TOKEN=r8_your_token_here
   ```
4. **Cost:** ~$0.01-0.05 per video generation

### Fal.ai API (Alternative)

1. **Sign up at [Fal.ai](https://fal.ai)**
2. **Get API key from dashboard**
3. **Add to .env:**
   ```
   LTX_VIDEO_PROVIDER=fal
   FAL_KEY=your_fal_key_here
   ```
4. **Cost:** ~$0.02-0.08 per video generation

### HuggingFace API (Free Tier Available)

1. **Sign up at [HuggingFace](https://huggingface.co)**
2. **Get token from settings**
3. **Add to .env:**
   ```
   LTX_VIDEO_PROVIDER=huggingface
   HUGGINGFACE_API_TOKEN=hf_your_token_here
   ```
4. **Cost:** Free tier available, then paid

## 🔧 cPanel Deployment Steps

### Step 1: Upload Files
```bash
# Upload your Laravel project to public_html or subdirectory
# Ensure these new files are included:
- app/Services/CloudLTXVideoService.php
- Updated MarketingController.php
- Updated services.php config
```

### Step 2: Environment Configuration
```bash
# In your cPanel File Manager, edit .env file
# Add the LTX Video environment variables based on your chosen provider
```

### Step 3: File Permissions
```bash
# Ensure storage directories have correct permissions
chmod 755 storage/app/public/generated_videos
```

### Step 4: Symlink Storage (if not already done)
```bash
php artisan storage:link
```

### Step 5: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## 🧪 Testing the Integration

### Test with Mock Provider
```bash
# Set in .env
LTX_VIDEO_PROVIDER=mock

# This will simulate video generation without API calls
# Perfect for testing the UI and workflow
```

### Test with Real API
```bash
# Set your chosen provider and API key
# Create a test post in social media section
# Click "Generate Video" button
```

## 💰 Cost Estimation

### Production Usage
- **Small business (10 videos/month):** $0.50 - $2.00/month
- **Medium business (50 videos/month):** $2.50 - $10.00/month  
- **Large business (200 videos/month):** $10.00 - $40.00/month

### Free Options
- **Mock Provider:** Completely free (for development)
- **HuggingFace Free Tier:** Limited free usage
- **Local Installation:** Free but requires GPU server

## 🔒 Security Considerations

### API Key Protection
```bash
# Never commit API keys to version control
# Use environment variables only
# Restrict API key permissions if possible
```

### File Storage
```bash
# Generated videos are stored in storage/app/public/generated_videos
# Consider setting up automatic cleanup
# Monitor disk space usage
```

## 🐛 Troubleshooting

### Common Issues

1. **"Video generation failed" error**
   - Check API key is correct
   - Verify provider is set correctly
   - Check API quota/billing

2. **"No accounts connected" warning**
   - Set up social media connections first
   - Check database migrations ran successfully

3. **File permission errors**
   - Ensure storage directories are writable
   - Check PHP file upload limits

4. **Timeout errors**
   - Increase PHP max_execution_time
   - Consider using queues for long operations

### Debug Mode
```bash
# Add to .env for debugging
APP_DEBUG=true
LOG_LEVEL=debug

# Check logs in storage/logs/laravel.log
```

## 📈 Performance Optimization

### Queue Implementation (Recommended)
```bash
# For better user experience, implement queue jobs
# Add to .env
QUEUE_CONNECTION=database

# Run queue worker
php artisan queue:work
```

### Caching
```bash
# Cache video generation results
# Implement Redis if available on hosting
CACHE_DRIVER=redis
```

## 🔄 Automatic Deployment

### GitHub Actions Example
```yaml
# .github/workflows/deploy.yml
name: Deploy to cPanel
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Deploy via FTP
        # Your deployment script here
        # Automatically updates environment variables
```

## 📞 Support

### Getting Help
1. **Check logs:** `storage/logs/laravel.log`
2. **Test with mock provider first**
3. **Verify API credentials**
4. **Check hosting provider PHP version (8.1+ required)**

### Monitoring
- Set up log monitoring for API failures
- Monitor video generation costs
- Track user engagement with generated videos

---

## 🎯 Quick Start Summary

1. **Choose Provider:** Replicate (recommended) or Mock (testing)
2. **Get API Key:** Sign up and get credentials
3. **Update .env:** Add provider and API key
4. **Test:** Create a post and generate video
5. **Monitor:** Check costs and performance

The system will automatically:
- ✅ Detect cloud vs local environment
- ✅ Use appropriate API provider
- ✅ Handle errors gracefully
- ✅ Store videos locally after generation
- ✅ Clean up old videos automatically
