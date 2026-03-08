<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "=== Email Configuration Test ===\n\n";

// 1. Check mail configuration
echo "Current Mail Configuration:\n";
echo "  Mailer: " . config('mail.default') . "\n";
echo "  Host: " . config('mail.mailers.smtp.host') . "\n";
echo "  Port: " . config('mail.mailers.smtp.port') . "\n";
echo "  Username: " . config('mail.mailers.smtp.username') . "\n";
echo "  Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "  From Address: " . config('mail.from.address') . "\n";
echo "  From Name: " . config('mail.from.name') . "\n\n";

// 2. Test sending to both domains
$testEmails = [
    'internal' => 'test@shopybook.com',
    'gmail' => 'harveyelvis24@gmail.com'
];

foreach ($testEmails as $type => $email) {
    echo "Testing email to {$type} ({$email})...\n";
    
    try {
        Mail::raw("This is a test email from Shopybook.\n\nTimestamp: " . now() . "\nType: {$type}", function ($message) use ($email) {
            $message->to($email)
                    ->subject('Shopybook Email Test - ' . now());
        });
        
        echo "  ✓ Email queued/sent successfully\n";
        
    } catch (\Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== Recommendations ===\n\n";
echo "If Gmail emails are not arriving, check:\n\n";
echo "1. SPF Record for shopybook.com:\n";
echo "   Add this TXT record to your DNS:\n";
echo "   v=spf1 mx a ip4:YOUR_SERVER_IP ~all\n\n";

echo "2. DKIM Configuration:\n";
echo "   Configure DKIM keys in your email server\n\n";

echo "3. DMARC Policy:\n";
echo "   Add DMARC record: _dmarc.shopybook.com\n";
echo "   v=DMARC1; p=none; rua=mailto:support@shopybook.com\n\n";

echo "4. Alternative: Use a transactional email service:\n";
echo "   - SendGrid (free tier: 100 emails/day)\n";
echo "   - Mailgun (free tier: 5,000 emails/month)\n";
echo "   - AWS SES (very cheap)\n";
echo "   - Postmark\n";
echo "   - Resend (modern, developer-friendly)\n\n";

echo "5. Check hosting provider restrictions:\n";
echo "   Some shared hosts block external email delivery\n";
echo "   Contact your hosting support to verify\n\n";

echo "=== End of Test ===\n";
