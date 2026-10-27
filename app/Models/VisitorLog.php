<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VisitorLog extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
        'url',
        'method',
        'user_id',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public $timestamps = false;

    public static function getTopIPs($limit = 10, $days = 7)
    {
        return Cache::remember("top_ips_{$limit}_{$days}", 600, function () use ($limit, $days) {
            return self::select('ip_address', DB::raw('COUNT(*) as visits'))
                ->where('visited_at', '>=', now()->subDays($days))
                ->groupBy('ip_address')
                ->orderBy('visits', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public static function getTodayStats()
    {
        return Cache::remember('visitor_today_stats', 120, function () {
            return [
                'total_visits' => self::whereDate('visited_at', today())->count(),
                'unique_ips' => self::whereDate('visited_at', today())->distinct('ip_address')->count(),
                'top_pages' => self::select('url', DB::raw('COUNT(*) as visits'))
                    ->whereDate('visited_at', today())
                    ->groupBy('url')
                    ->orderBy('visits', 'desc')
                    ->limit(5)
                    ->get(),
            ];
        });
    }
}
