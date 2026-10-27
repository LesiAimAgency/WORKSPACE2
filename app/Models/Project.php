<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'code',
        'name',
        'domain',
        'features',
        'status',
        'tenant_id',
    ];

    protected $casts = [
        'features' => 'array',
        'status' => 'string',
    ];

    /**
     * Lấy dự án hiện tại (Single Project: Đại Phúc 68)
     */
    public static function current(): ?self
    {
        // 1. Service container
        if (app()->bound('current_project')) {
            $project = app('current_project');
            if ($project instanceof self) {
                return $project;
            }
        }

        // 2. Request attribute
        if (request() && request()->attributes->has('project')) {
            $project = request()->attributes->get('project');
            if ($project instanceof self) {
                return $project;
            }
        }

        // 3. Fallback: single active project in DB
        return static::where('status', 'active')->first() ?? static::first();
    }

    /**
     * Bảng cấu hình bổ sung theo key-value của dự án
     */
    public function settings(): HasMany
    {
        return $this->hasMany(ProjectSetting::class, 'project_id');
    }

    /**
     * Bài viết thuộc dự án
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'project_id');
    }

    /**
     * Danh mục và phân loại của dự án
     */
    public function taxonomies(): HasMany
    {
        return $this->hasMany(Taxonomy::class, 'project_id');
    }

    /**
     * Widgets hiển thị trên giao diện của dự án
     */
    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class, 'project_id');
    }

    /**
     * Menus điều hướng của dự án
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'project_id');
    }

    /**
     * Nhật ký hoạt động của dự án
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'project_id');
    }

    /**
     * Lấy tên theme đang kích hoạt (mặc định daiphuc68)
     */
    public function getTheme(): string
    {
        $features = is_array($this->features) ? $this->features : json_decode($this->features ?? '[]', true);

        return $features['theme'] ?? 'daiphuc68';
    }

    /**
     * Accessor cho theme ($project->theme)
     */
    public function getThemeAttribute(): string
    {
        return $this->getTheme();
    }

    /**
     * Kiểm tra tính năng có được kích hoạt không (e.g. ecommerce, blog...)
     */
    public function hasFeature(string $feature): bool
    {
        $features = is_array($this->features) ? $this->features : json_decode($this->features ?? '[]', true);

        if (isset($features[$feature])) {
            return (bool) $features[$feature];
        }

        return in_array($feature, $features, true);
    }

    /**
     * Lấy giá trị cấu hình một feature cụ thể
     */
    public function getFeature(string $key, $default = null)
    {
        $features = is_array($this->features) ? $this->features : json_decode($this->features ?? '[]', true);

        return $features[$key] ?? $default;
    }

    /**
     * Kiểm tra trạng thái hoạt động của dự án
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Xác nhận dự án đang chạy độc lập (Single Project)
     */
    public function isStandalone(): bool
    {
        return true;
    }

    /**
     * Tương thích ngược: Single project không còn chạy chế độ multi-tenancy
     */
    public function isMultiTenancy(): bool
    {
        return false;
    }

    /**
     * Accessor domain an toàn
     */
    public function getDomainAttribute(?string $value): ?string
    {
        return $value ?: 'localhost';
    }

    /**
     * Tương thích ngược an toàn cho các view/code cũ nếu còn gọi $project->tenant
     */
    public function getTenantAttribute(): ?object
    {
        return null;
    }

    public function tenant()
    {
        return null;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeStandard($query)
    {
        return $query;
    }
}
