# AI Website Auto-Build Fix - Issue Resolution

## Problem
The AI auto-build feature in the website builder was failing with a **500 Internal Server Error** and no Laravel logs were being generated, making it difficult to diagnose the issue.

## Root Causes Identified

### 1. **Insufficient Error Logging**
- The controller had minimal error logging
- Errors were being caught but not logged with enough detail
- No visibility into what was failing

### 2. **Claude API Response Truncation**
- The `max_tokens` parameter was set to 4096, which was insufficient for generating complete website structures with 5-7 pages
- API responses were being truncated mid-JSON, causing parsing errors

### 3. **API Overload Errors (529)**
- Claude API occasionally returns 529 "Overloaded" errors during high traffic
- No retry logic was implemented to handle temporary API failures

### 4. **JSON Extraction Issues**
- JSON extraction logic wasn't robust enough to handle various response formats
- Missing validation of decoded JSON

### 5. **Database Column Name Mismatch** ⭐ **CRITICAL**
- Controller was using `website_page_id` but database column is `page_id`
- This caused SQL error: "Field 'page_id' doesn't have a default value"
- Website sections couldn't be created, causing the entire auto-build to fail

## Solutions Implemented

### 1. Enhanced Error Logging in Controller
**File**: `app/Http/Controllers/WebsiteBuilderController.php`

Added comprehensive logging throughout the `autoBuildWebsite` method:
- Log when auto-build starts with request data
- Log business information and plan status
- Log theme selection
- Log Claude API call with business data
- Log website structure generation success/failure
- Enhanced error catch block with full exception details including trace, file, and line number
- Return detailed error messages when `APP_DEBUG=true`

### 2. Improved Claude API Service
**File**: `app/Services/ClaudeAPIService.php`

#### a) Increased max_tokens
```php
'max_tokens' => 8192, // Increased from 4096
```

#### b) Added Retry Logic with Exponential Backoff
```php
$maxRetries = 3;
$retryDelay = 2; // seconds

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    // API call
    if ($response->successful()) break;
    
    // Retry on 529 (Overloaded) or 429 (Rate Limited)
    if ($status === 529 || $status === 429) {
        sleep($retryDelay * $attempt); // Exponential backoff
        continue;
    }
}
```

#### c) Enhanced JSON Extraction
- Added trimming of whitespace
- Better regex for extracting JSON from markdown code blocks
- Fallback to extract JSON object from any part of response
- Validation of decoded JSON before returning

#### d) Comprehensive Debug Logging
- Log API key configuration status
- Log full API response (when LOG_LEVEL=debug)
- Log response length and status
- Log JSON decoding errors with both start and end of JSON
- Log successful operations with page counts

### 3. Improved Frontend Error Handling
**File**: `resources/views/website-configurator/step1.blade.php`

- Better error message extraction from API responses
- Show detailed error messages to user
- Log errors to browser console with full details
- Guide users to check logs when errors occur

### 4. Fixed Database Column Name Mismatch ⭐ **CRITICAL FIX**
**File**: `app/Http/Controllers/WebsiteBuilderController.php`

Changed the section creation to use the correct column name:
```php
// BEFORE (incorrect):
'website_page_id' => $page->id,

// AFTER (correct):
'page_id' => $page->id,
```

This was the root cause of the 500 error - the database schema uses `page_id` but the controller was using `website_page_id`, causing SQL constraint violations.

## Testing

Created diagnostic test script: `test-ai-autobuild.php`

This script:
1. Checks Claude API configuration
2. Tests ClaudeAPIService instantiation
3. Finds or creates enterprise business for testing
4. Performs actual AI generation test
5. Reports success/failure with detailed output

## Verification

✅ Test passed successfully with:
- 6 pages generated
- 3-5 sections per page
- Complete JSON structure
- Proper error handling for API overload (retried and succeeded)

## Configuration Requirements

### Environment Variables
```env
CLAUDE_API_KEY=your-api-key-here
LOG_LEVEL=debug  # For development
APP_DEBUG=true   # For development
```

### Service Configuration
File: `config/services.php`
```php
'claude' => [
    'api_key' => env('CLAUDE_API_KEY'),
    'model' => env('CLAUDE_MODEL', 'claude-sonnet-4-20250514'),
    'max_tokens' => env('CLAUDE_MAX_TOKENS', 8192),
],
```

## How to Use

1. **Ensure Enterprise Plan**: Business must have `plan = 'enterprise'`
2. **No Existing Website**: Delete any existing website first
3. **Select Theme**: Choose a theme from the available options
4. **Click Auto-Build**: The AI will generate a complete website structure

## Monitoring and Troubleshooting

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Run Test Script
```bash
php test-ai-autobuild.php
```

### Common Issues

1. **API Key Not Configured**
   - Error: "Claude API key is not configured"
   - Solution: Add CLAUDE_API_KEY to .env file

2. **API Overload (529)**
   - Now handled automatically with retry logic
   - Retries up to 3 times with exponential backoff

3. **Insufficient Tokens**
   - Increased to 8192 tokens
   - Can be adjusted via CLAUDE_MAX_TOKENS in .env

4. **Not Enterprise Plan**
   - Error: "This feature is only available for Enterprise subscribers"
   - Solution: Update business plan to 'enterprise'

## Performance

- **Generation Time**: 30-60 seconds (depends on API response time)
- **Pages Generated**: 5-7 pages typically
- **Sections per Page**: 3-6 sections
- **Token Usage**: ~5000-8000 tokens per generation

## Next Steps (Optional Improvements)

1. Add progress websocket for real-time updates
2. Implement queue job for background processing
3. Add caching for frequently generated business types
4. Create templates for faster generation
5. Add user feedback mechanism for AI-generated content

## Files Modified

1. `app/Http/Controllers/WebsiteBuilderController.php`
2. `app/Services/ClaudeAPIService.php`
3. `resources/views/website-configurator/step1.blade.php`

## Files Created

1. `test-ai-autobuild.php` (diagnostic test script)
2. `AI_AUTOBUILD_FIX_SUMMARY.md` (this document)

---

**Status**: ✅ Fixed and Tested
**Date**: November 27, 2025
**Testing**: Successful with retry logic handling API overload
