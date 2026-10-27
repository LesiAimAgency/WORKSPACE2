<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ProjectSetting extends Model
{
    use HasFactory;

    protected $table = 'project_settings';

    protected $fillable = [
        'project_id',
        'key',
        'value',
    ];

    /**
     * Thuộc về một dự án cụ thể
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Lấy cấu hình theo key (hỗ trợ gọi theo cả 2 cách:
     * - ProjectSetting::get('site_name', 'Default')
     * - ProjectSetting::get(1, 'site_name', 'Default')
     */
    public static function get($keyOrProjectId, $key = null, $default = null)
    {
        if (is_numeric($keyOrProjectId) && is_string($key)) {
            $projectId = (int) $keyOrProjectId;
            $settingKey = $key;
            $defaultValue = $default;
        } else {
            $projectId = Project::current()?->id ?? 1;
            $settingKey = (string) $keyOrProjectId;
            $defaultValue = $key;
        }

        $cacheKey = "project_setting_{$projectId}_{$settingKey}";

        return Cache::remember($cacheKey, 3600, function () use ($projectId, $settingKey, $defaultValue) {
            $setting = static::where('project_id', $projectId)
                ->where('key', $settingKey)
                ->first();

            return $setting ? $setting->value : $defaultValue;
        });
    }

    /**
     * Lưu hoặc cập nhật cấu hình:
     * - ProjectSetting::set('site_name', 'Đại Phúc 68')
     * - ProjectSetting::set(1, 'site_name', 'Đại Phúc 68')
     */
    public static function set($keyOrProjectId, $keyOrValue, $value = null)
    {
        if ($value !== null || (is_numeric($keyOrProjectId) && func_num_args() === 3)) {
            $projectId = (int) $keyOrProjectId;
            $settingKey = (string) $keyOrValue;
            $settingVal = $value;
        } else {
            $projectId = Project::current()?->id ?? 1;
            $settingKey = (string) $keyOrProjectId;
            $settingVal = $keyOrValue;
        }

        $valString = is_array($settingVal) || is_object($settingVal)
            ? json_encode($settingVal, JSON_UNESCAPED_UNICODE)
            : (string) $settingVal;

        $record = static::updateOrCreate(
            ['project_id' => $projectId, 'key' => $settingKey],
            ['value' => $valString]
        );

        Cache::forget("project_setting_{$projectId}_{$settingKey}");
        Cache::forget("project_all_settings_{$projectId}");

        return $record;
    }

    /**
     * Kiểm tra một cấu hình có tồn tại hay không
     */
    public static function has(string $key, ?int $projectId = null): bool
    {
        $pId = $projectId ?? (Project::current()?->id ?? 1);

        return static::where('project_id', $pId)->where('key', $key)->exists();
    }

    /**
     * Xóa một key cấu hình
     */
    public static function forget(string $key, ?int $projectId = null): bool
    {
        $pId = $projectId ?? (Project::current()?->id ?? 1);

        Cache::forget("project_setting_{$pId}_{$key}");
        Cache::forget("project_all_settings_{$pId}");

        return (bool) static::where('project_id', $pId)->where('key', $key)->delete();
    }

    /**
     * Lấy toàn bộ cấu hình dạng mảng key => value cho dự án
     */
    public static function allSettings(?int $projectId = null): array
    {
        $pId = $projectId ?? (Project::current()?->id ?? 1);

        return Cache::remember("project_all_settings_{$pId}", 3600, function () use ($pId) {
            return static::where('project_id', $pId)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Tự động xóa cache khi model thay đổi
     */
    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget("project_setting_{$model->project_id}_{$model->key}");
            Cache::forget("project_all_settings_{$model->project_id}");
        });

        static::deleted(function ($model) {
            Cache::forget("project_setting_{$model->project_id}_{$model->key}");
            Cache::forget("project_all_settings_{$model->project_id}");
        });
    }
}
