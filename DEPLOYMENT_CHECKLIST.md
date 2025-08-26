# Canadian AI Model Integration - Deployment Checklist

## 🚀 Deployment Overview
This checklist will guide you through deploying the Canadian MSME AI model integration for Shopybook.

## ✅ Pre-Deployment Checklist

### 1. Environment Setup
- [ ] Ensure PHP 8.1+ is installed
- [ ] Verify Laravel 11.x is properly configured
- [ ] Check that Composer dependencies are installed
- [ ] Confirm database (MySQL/SQLite) is accessible

### 2. Python Environment
- [ ] Install Python 3.8+ 
- [ ] Install required Python packages:
  ```bash
  pip install pandas numpy scikit-learn matplotlib seaborn
  ```
- [ ] Verify Python is accessible from PHP (test with `python --version`)

### 3. AI Model Files
- [ ] Place `Shopybookbusinessanalyst_local.py` in `shopybookaimodels/` directory
- [ ] Place `data_dictionary.py` in `shopybookaimodels/` directory
- [ ] Place Canadian MSME data file (`2016 MSME Survey ver. 1.0.dta`) in `shopybookaimodels/` directory
- [ ] Ensure files have proper read permissions

### 4. Configuration Files
- [ ] Update `.env` file with AI configuration:
  ```env
  AI_DEFAULT_MODEL=canadian_msme
  AI_CANADIAN_MODEL_ENABLED=true
  AI_PYTHON_PATH=python
  AI_CACHE_RESULTS=true
  AI_CACHE_DURATION=3600
  AI_MAX_ANALYSIS_TIME=300
  AI_LOGGING_ENABLED=true
  AI_STORE_PREDICTIONS=true
  ```

## 🛠️ Deployment Steps

### Step 1: Run Setup Command
```bash
php artisan ai:setup-canadian-model
```
This command will:
- Run database migrations
- Check Python environment
- Verify model files
- Create required directories
- Test basic functionality

### Step 2: Run Database Migrations
```bash
php artisan migrate
```

### Step 3: Test the Integration
```bash
php artisan ai:test-canadian-model
```

### Step 4: Clear Application Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🧪 Testing & Validation

### Basic Functionality Test
1. **Database Connection**
   - [ ] Verify AI tables are created (`ai_business_analysis`, `ai_business_recommendations`, `ai_model_performance`)
   - [ ] Check table structures and indexes

2. **Python Integration**
   - [ ] Test Python execution from PHP
   - [ ] Verify model file accessibility
   - [ ] Check data processing pipeline

3. **API Endpoints**
   - [ ] Test enhanced analysis endpoint: `/api/business/analysis/enhanced`
   - [ ] Test financial analysis: `/api/business/analysis/financial`
   - [ ] Test benchmark comparison: `/api/business/analysis/benchmark`

4. **User Interface**
   - [ ] Access enhanced dashboard at `/business/analysis`
   - [ ] Test analysis generation
   - [ ] Verify recommendation display
   - [ ] Check export functionality

### Sample Test Commands
```bash
# Test Canadian model specifically
php artisan ai:test-canadian-model --business-id=1

# Test with sample data
php artisan ai:test-canadian-model --sample-data

# Run full integration test
php artisan test --filter=CanadianAITest
```

## 🔧 Configuration Options

### Environment Variables
| Variable | Default | Description |
|----------|---------|-------------|
| `AI_DEFAULT_MODEL` | `canadian_msme` | Primary AI model to use |
| `AI_CANADIAN_MODEL_ENABLED` | `true` | Enable Canadian model |
| `AI_PYTHON_PATH` | `python` | Path to Python executable |
| `AI_CACHE_RESULTS` | `true` | Cache analysis results |
| `AI_CACHE_DURATION` | `3600` | Cache duration in seconds |
| `AI_MAX_ANALYSIS_TIME` | `300` | Maximum analysis time (seconds) |
| `AI_LOGGING_ENABLED` | `true` | Enable AI operation logging |
| `AI_STORE_PREDICTIONS` | `true` | Store predictions in database |

### Advanced Configuration (config/ai.php)
```php
'models' => [
    'canadian_msme' => [
        'enabled' => env('AI_CANADIAN_MODEL_ENABLED', true),
        'script_path' => base_path('shopybookaimodels/Shopybookbusinessanalyst_local.py'),
        'data_path' => base_path('shopybookaimodels/2016 MSME Survey ver. 1.0.dta'),
        'python_path' => env('AI_PYTHON_PATH', 'python'),
        'timeout' => env('AI_MAX_ANALYSIS_TIME', 300),
        'cache_enabled' => env('AI_CACHE_RESULTS', true),
        'cache_duration' => env('AI_CACHE_DURATION', 3600),
    ]
]
```

## 🔍 Troubleshooting

### Common Issues

1. **Python Not Found**
   - Update `AI_PYTHON_PATH` in `.env`
   - Ensure Python is in system PATH
   - Use full path to Python executable

2. **Model Files Missing**
   - Verify files are in `shopybookaimodels/` directory
   - Check file permissions (read access required)
   - Ensure proper file naming

3. **Database Connection Issues**
   - Check database configuration in `.env`
   - Verify database user permissions
   - Run migrations manually if needed

4. **Analysis Timeouts**
   - Increase `AI_MAX_ANALYSIS_TIME` value
   - Check system resources
   - Consider using queue workers for large analyses

5. **Memory Issues**
   - Increase PHP memory limit
   - Consider processing data in chunks
   - Monitor Python process memory usage

### Debug Commands
```bash
# Check AI configuration
php artisan ai:config-check

# View AI logs
tail -f storage/logs/ai/ai.log

# Clear AI cache
php artisan ai:clear-cache

# Regenerate model cache
php artisan ai:refresh-model-cache
```

## 📊 Performance Optimization

### For Production
1. **Enable Caching**
   - Set `AI_CACHE_RESULTS=true`
   - Configure appropriate cache duration
   - Use Redis for better cache performance

2. **Queue Processing**
   - Use Laravel queues for long-running analyses
   - Configure queue workers for AI jobs
   - Monitor queue performance

3. **Database Optimization**
   - Add indexes for frequently queried AI data
   - Consider archiving old analysis data
   - Optimize database queries

4. **Resource Management**
   - Monitor Python process memory usage
   - Set appropriate timeouts
   - Consider horizontal scaling for heavy workloads

## 🔐 Security Considerations

1. **Data Protection**
   - Enable data encryption for sensitive information
   - Mask PII in logs and outputs
   - Implement proper access controls

2. **File Security**
   - Restrict access to model files
   - Validate input data before processing
   - Sanitize file paths and names

3. **API Security**
   - Implement rate limiting on AI endpoints
   - Use authentication for API access
   - Log and monitor AI usage

## 📈 Monitoring & Maintenance

### Key Metrics to Monitor
- Analysis completion rate
- Average processing time
- Model prediction accuracy
- Cache hit rate
- Error frequency

### Regular Maintenance
- Review and archive old analysis data
- Update model training data periodically
- Monitor system resource usage
- Update Python dependencies

### Logging
AI operations are logged to:
- `storage/logs/ai/ai.log` - General AI operations
- `storage/logs/laravel.log` - Laravel application logs
- Database tables for analysis history

## 🎯 Success Criteria
- [ ] All tests pass successfully
- [ ] Canadian model generates accurate business analyses
- [ ] UI displays analysis results properly
- [ ] Performance meets requirements (< 60 seconds for typical analysis)
- [ ] Error handling works correctly with OpenAI fallback
- [ ] Data is properly cached and stored

## 📞 Support Resources

### Documentation
- Laravel AI Integration Guide
- Canadian MSME Model Documentation
- Shopybook Business Analysis API Reference

### Contact
For issues with the integration, check:
1. Laravel logs in `storage/logs/`
2. AI-specific logs in `storage/logs/ai/`
3. Database for stored error messages
4. Python script output for debugging

---

**Note**: This integration replaces the previous OpenAI-based analysis system while maintaining OpenAI as a fallback option for reliability.
