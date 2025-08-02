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
        'provider' => env('LTX_VIDEO_PROVIDER', 'huggingface'), // replicate, fal, huggingface, mock
        'api_endpoint' => env('LTX_VIDEO_API_ENDPOINT'),
        'api_key' => env('LTX_VIDEO_API_KEY'),
        'replicate_version' => env('LTX_REPLICATE_VERSION', 'lightricks/ltx-video:13b-distilled'),
        
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
        'api_key' => env('HUGGINGFACE_API_TOKEN', 'REMOVED_SECRET_TOKEN'),
    ],

];
