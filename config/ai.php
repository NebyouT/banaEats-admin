<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI provider that will be used by the
    | AI service. You may override this value on a per-request basis.
    | Supported providers: "openai", "gemini"
    |
    */

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your OpenAI API settings. This will be used
    | to authenticate with the OpenAI API for ChatGPT functionality.
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 60),
        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Google Gemini API settings. This will be
    | used to authenticate with the Google AI API for Gemini functionality.
    |
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 60),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
        'vision_model' => env('GEMINI_VISION_MODEL', 'gemini-pro-vision'),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'top_p' => env('GEMINI_TOP_P', 0.8),
        'top_k' => env('GEMINI_TOP_K', 40),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Features Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure which AI features are enabled for each provider.
    | This allows you to enable/disable specific features based on provider.
    |
    */

    'features' => [
        'text_generation' => [
            'openai' => true,
            'gemini' => true,
        ],
        'image_analysis' => [
            'openai' => false, // OpenAI requires separate vision API
            'gemini' => true,
        ],
        'multimodal' => [
            'openai' => false,
            'gemini' => true,
        ],
        'code_generation' => [
            'openai' => true,
            'gemini' => true,
        ],
        'translation' => [
            'openai' => true,
            'gemini' => true,
        ],
        'summarization' => [
            'openai' => true,
            'gemini' => true,
        ],
        'sentiment_analysis' => [
            'openai' => true,
            'gemini' => true,
        ],
        'content_moderation' => [
            'openai' => true,
            'gemini' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Configure fallback behavior when the primary provider fails.
    | You may specify a fallback provider and enable/disable fallback.
    |
    */

    'fallback' => [
        'enabled' => env('AI_FALLBACK_ENABLED', true),
        'provider' => env('AI_FALLBACK_PROVIDER', 'gemini'),
        'retry_attempts' => env('AI_FALLBACK_RETRIES', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for AI requests to prevent abuse and control costs.
    |
    */

    'rate_limiting' => [
        'enabled' => env('AI_RATE_LIMIT_ENABLED', true),
        'requests_per_minute' => env('AI_REQUESTS_PER_MINUTE', 60),
        'requests_per_hour' => env('AI_REQUESTS_PER_HOUR', 1000),
        'requests_per_day' => env('AI_REQUESTS_PER_DAY', 10000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for AI responses to improve performance and reduce costs.
    |
    */

    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', true),
        'ttl' => env('AI_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'ai_response:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for AI requests and responses for debugging and monitoring.
    |
    */

    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'level' => env('AI_LOG_LEVEL', 'info'),
        'include_request_data' => env('AI_LOG_REQUESTS', false),
        'include_response_data' => env('AI_LOG_RESPONSES', false),
    ],

];
