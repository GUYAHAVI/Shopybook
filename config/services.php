<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/contacts/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', 'https://shopybook.com/auth/facebook/callback'),
    ],
    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_REDIRECT_URI', 'https://shopybook.com/auth/linkedin/callback'),
    ],
    'x' => [
        'access_token' => env('x_ACCESS_TOKEN'),
        'access_token_secret' => env('x_ACCESS_TOKEN_SECRET'),
        'bearer_token' => env('x_BEARER_TOKEN'),
    ],
    'instagram' => [
        'client_id' => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
    ],
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
    ],

    'tiktok' => [
        'client_id' => env('TIKTOK_CLIENT_ID'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
    ],

    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'api_key' => env('YOUTUBE_API_KEY'),
    ],

    'pinterest' => [
        'client_id' => env('PINTEREST_CLIENT_ID'),
        'client_secret' => env('PINTEREST_CLIENT_SECRET'),
    ],

    'snapchat' => [
        'client_id' => env('SNAPCHAT_CLIENT_ID'),
        'client_secret' => env('SNAPCHAT_CLIENT_SECRET'),
    ],

    'whatsapp' => [
        'app_id' => env('WHATSAPP_APP_ID'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    ],

    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
    ],

    'reddit' => [
        'client_id' => env('REDDIT_CLIENT_ID'),
        'client_secret' => env('REDDIT_CLIENT_SECRET'),
        'user_agent' => env('REDDIT_USER_AGENT', 'Shopybook Social Media Manager v1.0'),
    ],

    'ltx_video' => [
        // Local installation (development)
        'path' => env('LTX_VIDEO_PATH'), // No default - will trigger cloud API if not set
        'python_executable' => env('LTX_VIDEO_PYTHON', 'python'),
        'model_config' => env('LTX_VIDEO_MODEL', 'ltxv-13b-0.9.7-distilled'),
        
        // Cloud API configuration (production)
        'provider' => env('LTX_VIDEO_PROVIDER', 'replicate'), // replicate, fal, huggingface, mock
        'api_endpoint' => env('LTX_VIDEO_API_ENDPOINT'),
        'api_key' => env('LTX_VIDEO_API_KEY'),
        'replicate_version' => env('LTX_REPLICATE_VERSION', 'fb18726ba67c5a66b37d475784c81db21bd1017e2b9e031bc19e7e3fc8ab1742'),
        
        // General settings
        'default_style' => env('LTX_VIDEO_DEFAULT_STYLE', 'professional'),
        'cleanup_days' => env('LTX_VIDEO_CLEANUP_DAYS', 7),
        'use_cloud' => env('LTX_VIDEO_USE_CLOUD', true), // Force cloud API usage
    ],
    
    // Cloud API providers
    'replicate' => [
        'api_key' => env('REPLICATE_API_TOKEN'),
    ],
    
    'fal' => [
        'api_key' => env('FAL_KEY'),
    ],
    
            'huggingface' => [
            'api_key' => env('HUGGINGFACE_API_TOKEN'),
    ],

    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'business_shortcode' => env('MPESA_BUSINESS_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
        'environment' => env('MPESA_ENVIRONMENT', 'sandbox'), // sandbox or live
        
        // URLs
        'auth_url' => env('MPESA_AUTH_URL', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'),
        'stk_push_url' => env('MPESA_STK_PUSH_URL', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'),
        'callback_url' => env('MPESA_CALLBACK_URL', 'https://shopybook.com/api/mpesa/callback'),
        
        // Transaction details
        'transaction_type' => env('MPESA_TRANSACTION_TYPE', 'CustomerPayBillOnline'),
        // 'amount' => env('MPESA_AMOUNT', 1), // Amount in KSh
        'account_reference' => env('MPESA_ACCOUNT_REFERENCE', 'Shopybook Premium'),
        'transaction_desc' => env('MPESA_TRANSACTION_DESC', 'Premium Plan Upgrade'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
        
        // URLs
        'auth_url' => env('PAYPAL_AUTH_URL', 'https://api-m.sandbox.paypal.com/v1/oauth2/token'),
        'orders_url' => env('PAYPAL_ORDERS_URL', 'https://api-m.sandbox.paypal.com/v2/checkout/orders'),
    ],

    'hostpinnacle' => [
        'api_url' => env('HOSTPINNACLE_API_URL', 'https://smsportal.hostpinnacle.co.ke/SMSApi/send'),
        'user_id' => env('HOSTPINNACLE_USER_ID'), // Your Host Pinnacle username
        'password' => env('HOSTPINNACLE_PASSWORD'), // Your Host Pinnacle password
        'sender_id' => env('HOSTPINNACLE_SENDER_ID', 'SHOPYBOOK'), // Your approved sender name
        'api_key' => env('HOSTPINNACLE_API_KEY'), // Optional: API key (not always required)
        'response_format' => env('HOSTPINNACLE_RESPONSE_FORMAT', 'json'), // json, xml, or text
    ],

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'model' => env('CLAUDE_MODEL', 'claude-sonnet-4-20250514'),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 4096),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
    ],

];
