<?php

// MODIFIED: 2025-01-21

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For project routes, get user from request attributes (set by CheckCmsRole middleware)
        $user = $this->attributes->get('auth_user');

        if ($user) {
            // Super admin, administrator, dev level users or users with admin/cms/dev roles have access
            if (isset($user->level) && in_array($user->level, [0, 1, 2])) {
                return true;
            }

            if (isset($user->role) && in_array($user->role, ['cms', 'admin', 'dev'])) {
                return true;
            }

            // Check if user has the specific permission
            return $user->hasPermission('manage_categories');
        }

        // Fallback to regular auth for non-project routes
        return auth()->check();
    }

    public function rules(): array
    {
        // Get category ID from route parameter
        $categoryId = null;
        if ($this->route('category')) {
            $categoryId = is_object($this->route('category'))
                ? $this->route('category')->id
                : $this->route('category');
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // We'll handle unique validation in controller to show warning alerts
            ],
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:taxonomies,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'parent_id.exists' => 'Danh mục cha không tồn tại.',
            'image.string' => 'Hình ảnh phải là đường dẫn URL hợp lệ.',
            'image.max' => 'Đường dẫn hình ảnh không được vượt quá 500 ký tự.',
            'meta_title.max' => 'Tiêu đề SEO không được vượt quá 255 ký tự.',
            'meta_description.max' => 'Mô tả SEO không được vượt quá 500 ký tự.',
        ];
    }
}
