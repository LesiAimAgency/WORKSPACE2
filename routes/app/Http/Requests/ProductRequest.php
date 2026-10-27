<?php

// MODIFIED: 2025-01-21

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            return $user->hasPermission('manage_products');
        }

        // Fallback to regular auth for non-project routes
        return auth()->check();
    }

    public function rules()
    {
        // Get product ID from route parameter
        $productParam = $this->route('product');
        $productId = is_object($productParam) ? $productParam->id : $productParam;
        $projectCode = $this->route('projectCode');

        // Dynamic table names based on context
        if ($projectCode) {
            // Project context - always use project tables
            $productsTable = 'products_enhanced';
            $categoriesTable = 'product_categories';
            $brandsTable = 'brands';
        } else {
            // Legacy CMS context (should not be used anymore)
            $productsTable = 'products';
            $categoriesTable = 'product_categories';
            $brandsTable = 'brands';
        }

        // File upload validation removed since we're using media manager

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique($productsTable, 'slug')->ignore($productId),
            ],
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique($productsTable, 'sku')->ignore($productId),
            ],
            'price' => 'nullable|numeric|min:0|max:9999999999999.99',
            'sale_price' => 'nullable|numeric|min:0|max:9999999999999.99|lt:price',
            'has_price' => 'boolean',
            'stock_quantity' => 'integer|min:0',
            'manage_stock' => 'boolean',
            'stock_status' => 'in:in_stock,out_of_stock,on_backorder',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'product_category_id' => "nullable|exists:{$categoriesTable},id",
            'brand_id' => "nullable|exists:{$brandsTable},id",
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'categories' => 'nullable|array',
            'categories.*' => "exists:{$categoriesTable},id",
            'brands' => 'nullable|array',
            'brands.*' => "exists:{$brandsTable},id",
            'focus_keyword' => 'nullable|string|max:255',
            'schema_type' => 'nullable|string|max:100',
            'canonical_url' => 'nullable|string|max:500',
            'noindex' => 'nullable',
            'language_id' => 'required|integer|min:1', // REQUIRED for multi-language support

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'sku.required' => 'Mã SKU là bắt buộc.',
            'sku.unique' => 'Mã SKU đã tồn tại.',
            'description.required' => 'Mô tả sản phẩm là bắt buộc.',
            'price.max' => 'Giá sản phẩm không được vượt quá 9,999,999,999,999.99 VNĐ.',
            'sale_price.max' => 'Giá khuyến mãi không được vượt quá 9,999,999,999,999.99 VNĐ.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
        ];
    }
}
