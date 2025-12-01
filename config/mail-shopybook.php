<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shopybook Email Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the email configuration for Shopybook domain.
    | Update your .env file with these settings:
    |
    | MAIL_MAILER=smtp
    | MAIL_HOST=mail.shopybook.com
    | MAIL_PORT=465
    | MAIL_USERNAME=support@shopybook.com
    | MAIL_PASSWORD=your_email_password
    | MAIL_ENCRYPTION=ssl
    | MAIL_FROM_ADDRESS=support@shopybook.com
    | MAIL_FROM_NAME="Shopybook"
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'mail.shopybook.com'),
            'port' => env('MAIL_PORT', 465),
            'encryption' => env('MAIL_ENCRYPTION', 'ssl'),
            'username' => env('MAIL_USERNAME', 'support@shopybook.com'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'support@shopybook.com'),
        'name' => env('MAIL_FROM_NAME', 'Shopybook'),
    ],

    'markdown' => [
        'theme' => env('MAIL_MARKDOWN_THEME', 'default'),
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
];
