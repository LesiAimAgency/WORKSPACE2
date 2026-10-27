<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Configuration
    |--------------------------------------------------------------------------
    */

    'admin' => [
        'per_page' => env('ADMIN_PER_PAGE', 20),
        'cache_ttl' => env('ADMIN_CACHE_TTL', 300), // 5 minutes
        'dashboard_cache_ttl' => env('DASHBOARD_CACHE_TTL', 30), // 30 seconds
    ],

    'media' => [
        'disk' => env('MEDIA_DISK', 'public'),
        'max_file_size' => env('MEDIA_MAX_SIZE', 10240), // KB
        'allowed_types' => [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
            'videos' => ['mp4', 'avi', 'mov', 'wmv', 'flv'],
            'audio' => ['mp3', 'wav', 'ogg', 'aac'],
        ],
        'image_sizes' => [
            'thumbnail' => [150, 150],
            'medium' => [300, 300],
            'large' => [800, 600],
        ],
        'ffmpeg_enabled' => env('FFMPEG_ENABLED', false),
        'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    ],

    'ecommerce' => [
        'currency' => env('SHOP_CURRENCY', 'VND'),
        'currency_symbol' => env('SHOP_CURRENCY_SYMBOL', '₫'),
        'price_placeholder' => env('PRICE_PLACEHOLDER', 'Liên hệ'),
        'tax_rate' => env('TAX_RATE', 0.1), // 10%
        'shipping_fee' => env('SHIPPING_FEE', 30000),
        'free_shipping_threshold' => env('FREE_SHIPPING_THRESHOLD', 500000),
    ],

    'seo' => [
        'meta_title_suffix' => env('SEO_TITLE_SUFFIX', ' | Agency CMS'),
        'meta_description_length' => 160,
        'meta_title_length' => 60,
        'sitemap_enabled' => env('SITEMAP_ENABLED', true),
        'robots_txt_enabled' => env('ROBOTS_TXT_ENABLED', true),
    ],

    'cache' => [
        'products_ttl' => env('CACHE_PRODUCTS_TTL', 3600), // 1 hour
        'categories_ttl' => env('CACHE_CATEGORIES_TTL', 7200), // 2 hours
        'posts_ttl' => env('CACHE_POSTS_TTL', 1800), // 30 minutes
        'settings_ttl' => env('CACHE_SETTINGS_TTL', 86400), // 24 hours
    ],

    'notifications' => [
        'email_enabled' => env('NOTIFICATIONS_EMAIL_ENABLED', true),
        'database_enabled' => env('NOTIFICATIONS_DATABASE_ENABLED', true),
        'slack_enabled' => env('NOTIFICATIONS_SLACK_ENABLED', false),
        'slack_webhook' => env('SLACK_WEBHOOK_URL'),
    ],

    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'disk' => env('BACKUP_DISK', 'local'),
        'schedule' => env('BACKUP_SCHEDULE', 'daily'),
        'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
        'include_files' => env('BACKUP_INCLUDE_FILES', false),
    ],

    'security' => [
        'rate_limit' => [
            'api' => env('RATE_LIMIT_API', 60), // per minute
            'login' => env('RATE_LIMIT_LOGIN', 5), // per minute
            'contact' => env('RATE_LIMIT_CONTACT', 3), // per minute
        ],
        'password_min_length' => env('PASSWORD_MIN_LENGTH', 8),
        'session_timeout' => env('SESSION_TIMEOUT', 120), // minutes
    ],

    'features' => [
        'reviews_enabled' => env('REVIEWS_ENABLED', true),
        'wishlist_enabled' => env('WISHLIST_ENABLED', true),
        'compare_enabled' => env('COMPARE_ENABLED', true),
        'newsletter_enabled' => env('NEWSLETTER_ENABLED', true),
        'multi_language' => env('MULTI_LANGUAGE_ENABLED', false),
        'rtl_support' => env('RTL_SUPPORT', false),
    ],

    'api' => [
        'version' => 'v1',
        'rate_limit' => env('API_RATE_LIMIT', 100), // per minute
        'pagination_limit' => env('API_PAGINATION_LIMIT', 50),
        'cache_ttl' => env('API_CACHE_TTL', 300), // 5 minutes
    ],
];
