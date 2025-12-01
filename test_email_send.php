<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Attempting to send test email...\n";

try {
    Mail::raw('This is a test email from Shopybook to verify Gmail delivery.', function ($message) {
        $message->to('harveyelvis24@gmail.com')
                ->subject('Test Email - Shopybook Verification Check')
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "✓ Email sent successfully!\n";
    echo "Mail driver: " . config('mail.default') . "\n";
    echo "Mail host: " . config('mail.mailers.smtp.host') . "\n";
    echo "Mail port: " . config('mail.mailers.smtp.port') . "\n";
    echo "Mail encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "From address: " . config('mail.from.address') . "\n";
    echo "\nCheck the Gmail inbox (and spam folder) for the test email.\n";
    
} catch (\Exception $e) {
    echo "✗ Email send failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nFull exception:\n";
    echo $e;
}
