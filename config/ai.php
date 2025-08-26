<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for various AI services
    | used in Shopybook, including the KENADA (Kenya National Data) MSME model and fallback options.
    |
    */

    'default_model' => env('AI_DEFAULT_MODEL', 'kenyan_msme'),

    'models' => [
        'kenyan_msme' => [
            'name' => 'KENADA MSME Business Analyst',
            'description' => 'AI model trained on Kenya National Data for accurate business analysis',
            'python_path' => env('AI_PYTHON_PATH', 'python'),
            'model_path' => base_path('shopybookaimodels'),
            'data_path' => storage_path('app/ai_data'),
            'enabled' => env('AI_KENYAN_MODEL_ENABLED', true),
            'confidence_threshold' => 0.8,
            'version' => '1.0.0',
        ],

        'openai' => [
            'name' => 'OpenAI GPT',
            'description' => 'Fallback AI service using OpenAI GPT models',
            'api_key' => env('OPENAI_API_KEY', ''),
            'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
            'enabled' => env('AI_OPENAI_ENABLED', true),
            'max_tokens' => env('OPENAI_MAX_TOKENS', 1500),
            'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        ],
    ],

    'features' => [
        'financial_analysis' => env('AI_FINANCIAL_ANALYSIS', true),
        'operational_analysis' => env('AI_OPERATIONAL_ANALYSIS', true),
        'growth_predictions' => env('AI_GROWTH_PREDICTIONS', true),
        'benchmark_comparison' => env('AI_BENCHMARK_COMPARISON', true),
        'automated_recommendations' => env('AI_AUTOMATED_RECOMMENDATIONS', true),
        'report_generation' => env('AI_REPORT_GENERATION', true),
    ],

    'data_sources' => [
        'kenyan_survey' => [
            'name' => '2016 MSME Survey',
            'description' => 'Kenya National Data - Micro, Small and Medium Enterprise Survey Data',
            'file_path' => env('AI_KENYAN_DATA_PATH', '2016 MSME Survey ver. 1.0.dta'),
            'enabled' => true,
        ],
    ],

    'performance' => [
        'cache_analysis_results' => env('AI_CACHE_RESULTS', true),
        'cache_duration' => env('AI_CACHE_DURATION', 3600), // 1 hour in seconds
        'max_analysis_time' => env('AI_MAX_ANALYSIS_TIME', 300), // 5 minutes in seconds
        'retry_attempts' => env('AI_RETRY_ATTEMPTS', 3),
    ],

    'notifications' => [
        'analysis_complete' => env('AI_NOTIFY_ANALYSIS_COMPLETE', true),
        'analysis_failed' => env('AI_NOTIFY_ANALYSIS_FAILED', true),
        'low_confidence_predictions' => env('AI_NOTIFY_LOW_CONFIDENCE', true),
    ],

    'business_types' => [
        'retail' => [
            'keywords' => ['retail', 'shop', 'store', 'boutique', 'supermarket'],
            'benchmarks' => [
                'revenue_per_employee' => 250000,
                'profit_margin' => 15,
                'expense_ratio' => 75,
            ],
        ],
        'service' => [
            'keywords' => ['service', 'consulting', 'professional', 'agency'],
            'benchmarks' => [
                'revenue_per_employee' => 180000,
                'profit_margin' => 20,
                'expense_ratio' => 70,
            ],
        ],
        'manufacturing' => [
            'keywords' => ['manufacturing', 'production', 'factory', 'workshop'],
            'benchmarks' => [
                'revenue_per_employee' => 300000,
                'profit_margin' => 12,
                'expense_ratio' => 80,
            ],
        ],
        'food' => [
            'keywords' => ['food', 'restaurant', 'cafe', 'catering', 'bakery'],
            'benchmarks' => [
                'revenue_per_employee' => 200000,
                'profit_margin' => 18,
                'expense_ratio' => 72,
            ],
        ],
        'technology' => [
            'keywords' => ['technology', 'software', 'IT', 'digital', 'tech'],
            'benchmarks' => [
                'revenue_per_employee' => 350000,
                'profit_margin' => 25,
                'expense_ratio' => 65,
            ],
        ],
    ],

    'recommendations' => [
        'priority_levels' => ['high', 'medium', 'low'],
        'categories' => [
            'revenue_optimization',
            'cost_management',
            'operational_efficiency',
            'growth_opportunities',
            'risk_factors',
            'market_expansion',
            'technology_adoption',
            'staff_development',
        ],
        'max_recommendations_per_category' => 5,
    ],

    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'level' => env('AI_LOG_LEVEL', 'info'),
        'store_predictions' => env('AI_STORE_PREDICTIONS', true),
        'store_model_performance' => env('AI_STORE_MODEL_PERFORMANCE', true),
    ],

    'security' => [
        'encrypt_analysis_data' => env('AI_ENCRYPT_DATA', false),
        'mask_sensitive_data' => env('AI_MASK_SENSITIVE_DATA', true),
        'data_retention_days' => env('AI_DATA_RETENTION_DAYS', 90),
    ],

];
