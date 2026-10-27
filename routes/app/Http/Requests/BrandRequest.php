<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For project routes, get user from request attributes (set by CheckCmsRole middleware)
        $user = $this->attributes->get('auth_user');

        if ($user) {
            if (isset($user->level) && in_array($user->level, [0, 1, 2])) {
                return true;
            }

            if (isset($user->role) && in_array($user->role, ['cms', 'admin', 'dev'])) {
                return true;
            }

            // Check if user has the specific permission
            return $user->hasPermission('manage_brands');
        }

        // Fallback to regular auth for non-project routes
        return auth()->check();
    }

    public function rules(): array
    {
        $brandId = $this->route('brand') ?: null;

        // For ProjectBrand, we need to get the ID differently since we're not using route model binding
        $brandId = is_numeric($brandId) ? $brandId : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // We'll handle unique validation in controller to show warning alerts
            ],
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên thương hiệu là bắt buộc.',
            'slug.unique' => 'Slug thương hiệu đã tồn tại.',
            'logo.string' => 'Logo phải là đường dẫn URL hợp lệ.',
            'logo.max' => 'Đường dẫn logo không được vượt quá 500 ký tự.',
            'meta_title.max' => 'Tiêu đề SEO không được vượt quá 255 ký tự.',
            'meta_description.max' => 'Mô tả SEO không được vượt quá 500 ký tự.',
        ];
    }
}
