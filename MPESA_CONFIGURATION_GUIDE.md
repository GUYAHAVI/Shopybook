# M-Pesa Configuration Guide

## 🔧 Fixing the "Wrong Credentials" Error

The error you're seeing in the logs:
```
"errorCode": "500.001.1001",
"errorMessage": "Wrong credentials"
```

This occurs because the M-Pesa credentials are currently set to placeholder values. Here's how to fix it:

## 📋 Required Environment Variables

Add these to your `.env` file:

```env
# M-Pesa Configuration
MPESA_CONSUMER_KEY=your_actual_consumer_key
MPESA_CONSUMER_SECRET=your_actual_consumer_secret
MPESA_PASSKEY=your_actual_passkey
MPESA_SHORTCODE=174379
MPESA_ENVIRONMENT=sandbox
MPESA_CALLBACK_URL=https://shopybook.com/api/mpesa/callback

# PayPal Configuration (optional)
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_MODE=sandbox
PAYPAL_CURRENCY=USD
```

## 🚀 Getting M-Pesa Credentials

### 1. Sandbox Testing (Recommended for Development)

1. **Visit M-Pesa Developer Portal**: https://developer.safaricom.co.ke/
2. **Create Account**: Sign up for a developer account
3. **Create App**: Create a new application
4. **Get Test Credentials**: Use the sandbox credentials provided

### 2. Live Production Credentials

1. **Contact Safaricom**: Reach out to Safaricom Business Support
2. **Submit Application**: Provide business details and use case
3. **Get Approval**: Wait for approval (usually 1-2 weeks)
4. **Receive Credentials**: Get live consumer key, secret, and passkey

## 🔍 Current Configuration Status

The PaymentController has been updated to use environment variables instead of hardcoded placeholders. The configuration is now:

- ✅ **Environment Variables**: Using `env()` function
- ✅ **Fallback Values**: Default to placeholder values if not set
- ✅ **Flexible Configuration**: Easy to switch between sandbox and live

## 🧪 Testing the Configuration

After setting up your credentials:

1. **Clear Cache**: `php artisan config:clear`
2. **Test Payment**: Try initiating an M-Pesa payment
3. **Check Logs**: Monitor `storage/logs/laravel.log` for success/errors

## 📞 Support

If you need help getting M-Pesa credentials:

- **Developer Portal**: https://developer.safaricom.co.ke/
- **Business Support**: Contact Safaricom Business Support
- **Documentation**: https://developer.safaricom.co.ke/docs

## 🔒 Security Notes

- Never commit real credentials to version control
- Use different credentials for development and production
- Regularly rotate your credentials
- Monitor your API usage and logs

## 🎯 Next Steps

1. Add your M-Pesa credentials to `.env`
2. Test with sandbox environment first
3. Switch to live environment when ready for production
4. Monitor payment success rates and logs
