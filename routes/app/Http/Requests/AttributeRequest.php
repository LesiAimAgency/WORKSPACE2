<?php

// MODIFIED: 2025-01-21

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Simplified for now - CheckCmsRole middleware already handles auth
    }

    public function rules()
    {
        $attributeId = $this->route('attribute')?->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_attributes', 'slug')->ignore($attributeId),
            ],
            'attribute_group_id' => 'nullable|exists:attribute_groups,id',
            'type' => 'required|in:text,number,select,multiselect,color,boolean',
            'is_filterable' => 'boolean',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0',
            'values' => 'array',
            'values.*.value' => 'required_with:values|string|max:255',
            'values.*.display_value' => 'nullable|string|max:255',
            'values.*.color_code' => 'nullable|string|max:7',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên thuộc tính là bắt buộc.',
            'type.required' => 'Loại thuộc tính là bắt buộc.',
            'type.in' => 'Loại thuộc tính không hợp lệ.',
            'values.*.value.required_with' => 'Giá trị thuộc tính là bắt buộc.',
        ];
    }
}
