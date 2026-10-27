<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    const DASHBOARD_STATS = 'dashboard_stats';

    const VISITOR_STATS = 'visitor_stats';

    const TOP_IPS = 'top_ips';

    const PRODUCTS_LIST = 'products_list';

    const CATEGORIES_LIST = 'categories_list';

    public static function remember(string $key, int $minutes, callable $callback)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    public static function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    public static function flush(): bool
    {
        return Cache::flush();
    }

    public static function tags(array $tags)
    {
        return Cache::tags($tags);
    }
}
