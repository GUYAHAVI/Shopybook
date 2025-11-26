<?php

/**
 * SMTP Connection Test Script
 * Run this to test your mail.shopybook.com credentials
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Log;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== SMTP Connection Test ===\n\n";

$host = $_ENV['MAIL_HOST'] ?? 'mail.shopybook.com';
$port = $_ENV['MAIL_PORT'] ?? 465;
$username = $_ENV['MAIL_USERNAME'] ?? 'support@shopybook.com';
$password = $_ENV['MAIL_PASSWORD'] ?? '';
$encryption = $_ENV['MAIL_ENCRYPTION'] ?? 'ssl';

echo "Testing connection with:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Username: $username\n";
echo "Password: " . str_repeat('*', strlen($password)) . " (length: " . strlen($password) . ")\n";
echo "Encryption: $encryption\n\n";

try {
    // Create a Swift_SmtpTransport instance
    $transport = (new Swift_SmtpTransport($host, $port, $encryption))
        ->setUsername($username)
        ->setPassword($password)
        ->setStreamOptions([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

    // Create the Mailer using the transport
    $mailer = new Swift_Mailer($transport);

    // Test the connection
    echo "Attempting to connect...\n";
    $transport->start();
    
    echo "✓ SUCCESS! SMTP connection established successfully.\n\n";
    echo "Your email credentials are correct.\n";
    echo "The verification emails should work now.\n";

    $transport->stop();

} catch (Swift_TransportException $e) {
    echo "✗ FAILED! SMTP connection failed.\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "Common issues:\n";
    echo "1. Wrong password - Verify the password in cPanel/email settings\n";
    echo "2. Wrong port - Try port 587 with TLS instead of 465 with SSL\n";
    echo "3. Wrong host - Verify mail.shopybook.com is correct\n";
    echo "4. Firewall blocking - Check if port $port is open\n";
    echo "5. SSL certificate issues - Try disabling SSL verification\n\n";
    
    echo "Suggested .env changes:\n";
    echo "MAIL_PORT=587\n";
    echo "MAIL_ENCRYPTION=tls\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
