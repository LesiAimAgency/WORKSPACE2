<?php

// MODIFIED: 2025-01-21

return [
    'per_page' => env('ADMIN_PER_PAGE', 20),
    'price_placeholder' => env('PRICE_PLACEHOLDER', 'Liên hệ'),
    'cache_ttl' => env('ADMIN_CACHE_TTL', 30),

    'media' => [
        'max_file_size' => env('MEDIA_MAX_SIZE', 2048), // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'storage_disk' => env('MEDIA_DISK', 'public'),
    ],

    'backup' => [
        'disk' => env('BACKUP_DISK', 'local'),
        'schedule' => env('BACKUP_SCHEDULE', 'daily'),
    ],
];
