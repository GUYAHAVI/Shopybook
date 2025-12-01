# Shopybook AI Customer Support Chatbot

## Overview
An AI-powered customer support chatbot integrated into the Shopybook landing page using Claude API (Anthropic). The chatbot is specifically trained to answer questions about Shopybook features, pricing, and help potential customers.

## Components Created

### 1. Frontend Widget (`resources/views/components/chatbot.blade.php`)
A floating chatbot button that opens a chat window with:
- Toggle button with AI badge
- Chat window with header
- Message display area
- Input form
- Beautiful UI matching Shopybook branding (#020258 and #13e8e9)
- Responsive design (mobile-friendly)
- Typing indicators
- Smooth animations

### 2. Backend Controller (`app/Http/Controllers/ChatbotController.php`)
Handles chat requests with:
- Message validation
- Claude API integration
- Comprehensive Shopybook knowledge base
- Error handling and logging
- System prompt with all Shopybook features and information

### 3. Route Configuration
- **POST** `/chatbot/message` - Handles chat messages
- Protected with CSRF token
- Returns JSON responses

## Features

### Chatbot Knowledge Base
The chatbot knows about:

✅ **All Shopybook Features:**
- Product Management (with OCR)
- Service Booking System
- Point of Sale (POS)
- Customer CRM
- Staff Management
- Inventory Management
- AI Website Builder
- Business Analytics
- Marketing Automation
- Payment Integration (M-Pesa)
- AI Business Advisor (KENADA)
- Smart Notifications

✅ **Pricing Information:**
- Free Starter Plan (KSh 0/month)
- Business Pro Plan (KSh 500/month)
- Enterprise Plan (KSh 1,000/month)

✅ **Contact Information:**
- Email: info@shopybook.com
- Phone: +254 717745891
- WhatsApp: +254 717745891
- Website: shopybook.com

✅ **Technical Details:**
- Multi-tenant architecture
- Multi-language support (English, Swahili, Sheng)
- Mobile-first design
- M-Pesa integration

### Chatbot Behavior
- **Focused:** Only answers Shopybook-related questions
- **Helpful:** Provides concise, actionable information
- **Professional:** Friendly tone with proper guidance
- **Accurate:** Based on real platform features
- **Actionable:** Always provides next steps (sign up, contact support, etc.)

## Configuration


### Claude Model
- **Model:** claude-3-5-sonnet-20241022 (latest Sonnet version)
- **Max Tokens:** 1024 (suitable for concise responses)
- **API Version:** 2023-06-01

## Usage

### For Visitors
1. Click the floating chat button (bottom-right corner)
2. Type a question about Shopybook
3. Get instant AI-powered responses
4. Follow provided links/actions

### Example Questions
- "What features does Shopybook have?"
- "How much does it cost?"
- "Does Shopybook support M-Pesa?"
- "Can I create a website with Shopybook?"
- "How do I get started?"
- "What is KENADA?"
- "Do you support multiple languages?"

## Customization

### Update Chatbot Knowledge
To modify what the chatbot knows, edit the system prompt in:
```php
app/Http/Controllers/ChatbotController.php
```

Look for the `getShopybookSystemPrompt()` method and update the content.

### Change Chatbot Appearance
Edit the styles in:
```blade
resources/views/components/chatbot.blade.php
```

Key colors:
- Primary: `#020258` (dark blue)
- Accent: `#13e8e9` (cyan)
- Background: `#fff` (white)

### Adjust Response Length
Modify `max_tokens` in the controller:
```php
'max_tokens' => 1024, // Increase for longer responses
```

## Monitoring & Logging

### Error Logging
All errors are logged to Laravel's log system:
```bash
# View logs
tail -f storage/logs/laravel.log
```

### API Errors
Claude API errors include:
- Status code
- Response body
- Timestamp

### User Messages
Currently, messages are not stored. To add message storage:
1. Create a `chat_messages` table
2. Store messages in the controller
3. Add analytics dashboard

## Security

### CSRF Protection
All requests require valid CSRF token:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Input Validation
- Maximum message length: 1000 characters
- Required field validation
- SQL injection protection (Laravel)

### Rate Limiting
Consider adding rate limiting to prevent abuse:
```php
Route::post('/chatbot/message', [ChatbotController::class, 'message'])
    ->middleware('throttle:20,1') // 20 requests per minute
    ->name('chatbot.message');
```

## Testing

### Manual Testing
1. Visit landing page: https://shopybook.com
2. Click chatbot button
3. Try these test questions:
   - "What is Shopybook?"
   - "How much does it cost?"
   - "Tell me about M-Pesa integration"
   - "Random non-Shopybook question" (should politely decline)

### Expected Behavior
✅ Quick responses (< 3 seconds)
✅ Accurate Shopybook information
✅ Professional tone
✅ Actionable next steps
✅ Graceful error handling

### Check Logs
```bash
# Monitor real-time
tail -f storage/logs/laravel.log

# Search for errors
grep "Chatbot error" storage/logs/laravel.log
```

## Troubleshooting

### Chatbot Not Appearing
1. Clear browser cache
2. Check console for JavaScript errors
3. Verify chatbot.blade.php is included in index.blade.php

### "Chat service not configured" Error
- Verify `CLAUDE_API_KEY` in `.env`
- Ensure key is valid and active
- Check Laravel cache: `php artisan config:clear`

### API Timeout Errors
- Increase timeout in controller (currently 30 seconds)
- Check network connectivity
- Verify Claude API status

### Slow Responses
- Check server resources
- Verify network speed
- Consider caching common questions

### Bot Gives Wrong Information
- Update system prompt in controller
- Review and correct feature descriptions
- Test with corrected knowledge base

## Performance Optimization

### Caching Common Questions
Add caching for frequently asked questions:
```php
$cacheKey = 'chatbot_' . md5($userMessage);
$response = Cache::remember($cacheKey, 3600, function() {
    // Call Claude API
});
```

### Response Compression
Enable Gzip compression in your web server for faster loading.

### CDN for Static Assets
Use CDN for Bootstrap Icons and other libraries.

## Future Enhancements

### Potential Features
1. **Message History Storage**
   - Track conversations
   - Analytics on common questions
   - User satisfaction ratings

2. **Multilingual Support**
   - Detect user language
   - Respond in Swahili/Sheng
   - Translation integration

3. **Lead Capture**
   - Collect email during chat
   - CRM integration
   - Follow-up automation

4. **Advanced Analytics**
   - Popular questions dashboard
   - Conversion tracking
   - A/B testing different prompts

5. **Handoff to Human**
   - Escalate complex queries
   - Live chat integration
   - Business hours detection

6. **Voice Integration**
   - Text-to-speech responses
   - Voice input support
   - Accessibility improvements

## Cost Considerations

### Claude API Pricing
- **Input:** ~$3 per million tokens
- **Output:** ~$15 per million tokens
- **Average Cost per Conversation:** $0.01-0.05

### Monthly Estimates
- 1,000 conversations/month: ~$10-50
- 5,000 conversations/month: ~$50-250
- 10,000 conversations/month: ~$100-500

### Cost Optimization
1. Cache common questions
2. Limit max_tokens
3. Add rate limiting
4. Use Claude Haiku for simpler queries

## Support & Maintenance

### Regular Updates
- Update product knowledge monthly
- Review and improve system prompt
- Monitor conversation quality
- Update pricing information

### Monitoring Checklist
- [ ] Check error logs weekly
- [ ] Review common questions
- [ ] Test chatbot functionality
- [ ] Update knowledge base
- [ ] Monitor API costs

## Contact
For chatbot issues or improvements:
- Email: info@shopybook.com
- Phone: +254 717745891
- Developer: Harvey (GitHub: GUYAHAVI)

---

**Last Updated:** December 1, 2025
**Version:** 1.0.0
**Status:** ✅ Active and Ready
