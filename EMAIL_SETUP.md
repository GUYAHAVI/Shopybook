# Email Confirmation and Forgot Password Setup

This guide will help you set up email confirmation after registration and forgot password functionality using your Shopybook domain email.

## Features Implemented

1. **Email Confirmation After Registration**
   - Users must verify their email before accessing the application
   - Custom branded email templates
   - 60-minute expiration for verification links

2. **Forgot Password Functionality**
   - Password reset via email
   - Custom branded email templates
   - 60-minute expiration for reset links

## Email Configuration

### Update your `.env` file with these settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.shopybook.com
MAIL_PORT=465
MAIL_USERNAME=support@shopybook.com
MAIL_PASSWORD=your_email_password_here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=support@shopybook.com
MAIL_FROM_NAME="Shopybook"
```

### Email Server Settings (for reference):
- **Username:** support@shopybook.com
- **Password:** Use the email account's password
- **Incoming Server:** mail.shopybook.com
- **IMAP Port:** 993 | **POP3 Port:** 995
- **Outgoing Server:** mail.shopybook.com
- **SMTP Port:** 465
- **Encryption:** SSL/TLS

## Files Created/Modified

### New Files:
- `app/Notifications/CustomVerifyEmail.php` - Custom email verification notification
- `app/Notifications/CustomResetPassword.php` - Custom password reset notification
- `app/Console/Commands/TestEmailConfiguration.php` - Test email command
- `resources/views/auth/verify.blade.php` - Email verification notice page
- `resources/views/vendor/mail/html/themes/default.css` - Custom email theme
- `config/mail-shopybook.php` - Email configuration reference

### Modified Files:
- `app/Models/User.php` - Added email verification interface and custom notifications
- `app/Http/Controllers/Auth/RegisterController.php` - Added email verification after registration
- `app/Http/Controllers/Auth/VerificationController.php` - Updated redirect path

## Testing the Setup

### 1. Test Email Configuration
Run this command to test if your email configuration is working:

```bash
php artisan email:test your-email@example.com
```

### 2. Test Registration Flow
1. Register a new user account
2. Check your email for the verification link
3. Click the verification link
4. You should be redirected to the business type selection page

### 3. Test Forgot Password Flow
1. Go to the login page
2. Click "Forgot Your Password?"
3. Enter your email address
4. Check your email for the password reset link
5. Click the link and set a new password

## Routes Available

The following routes are automatically available through `Auth::routes()`:

### Email Verification:
- `GET /email/verify` - Show email verification notice
- `GET /email/verify/{id}/{hash}` - Verify email address
- `POST /email/resend` - Resend verification email

### Password Reset:
- `GET /password/reset` - Show password reset form
- `POST /password/email` - Send password reset email
- `GET /password/reset/{token}` - Show password reset form with token
- `POST /password/reset` - Reset password

## Customization

### Email Templates
You can customize the email templates by modifying:
- `app/Notifications/CustomVerifyEmail.php`
- `app/Notifications/CustomResetPassword.php`

### Email Theme
Customize the email appearance by editing:
- `resources/views/vendor/mail/html/themes/default.css`

### Redirect Paths
- After email verification: `/business/choose-type`
- After registration: Email verification notice page

## Troubleshooting

### Common Issues:

1. **Email not sending:**
   - Check your email credentials in `.env`
   - Verify SMTP settings
   - Check if your hosting provider allows SMTP connections

2. **Verification links not working:**
   - Ensure your app URL is correctly set in `.env`
   - Check if the verification routes are accessible

3. **Emails going to spam:**
   - Configure SPF and DKIM records for your domain
   - Use a reputable email service

### Debug Commands:
```bash
# Test email configuration
php artisan email:test your-email@example.com

# Clear cache
php artisan config:clear
php artisan cache:clear

# View all routes
php artisan route:list
```

## Security Notes

- Email verification links expire after 60 minutes
- Password reset links expire after 60 minutes
- All links are signed to prevent tampering
- Rate limiting is applied to prevent abuse

## Next Steps

1. Update your `.env` file with the email settings
2. Test the email configuration
3. Test the registration and password reset flows
4. Customize email templates if needed
5. Monitor email delivery and spam folder placement
