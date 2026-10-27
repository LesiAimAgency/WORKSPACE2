<?php

namespace App\Http\Requests;

use App\Models\ProductAttributeValue;
use Illuminate\Foundation\Http\FormRequest;

class AttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Simplified - CheckCmsRole middleware already handles auth
    }

    public function rules()
    {
        $attributeId = $this->route('attributeId') ?? $this->product_attribute_id;
        $valueId = $this->route('valueId');

        return [
            'product_attribute_id' => 'required|integer',
            'value' => [
                'required',
                'string',
                'max:255',
                // Ensure value is unique within the same attribute
                function ($attribute, $value, $fail) use ($attributeId, $valueId) {
                    $query = ProductAttributeValue::where('product_attribute_id', $attributeId)
                        ->where('value', $value);

                    // Exclude current record when updating
                    if ($valueId) {
                        $query->where('id', '!=', $valueId);
                    }

                    if ($query->exists()) {
                        $fail('Giá trị này đã tồn tại trong thuộc tính.');
                    }
                },
            ],
            'display_value' => 'nullable|string|max:255',
            'color_code' => 'nullable|regex:/^#[0-9A-F]{6}$/i',
            'sort_order' => 'integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'product_attribute_id.required' => 'Thuộc tính là bắt buộc.',
            'value.required' => 'Giá trị thuộc tính là bắt buộc.',
            'color_code.regex' => 'Mã màu phải là hex color hợp lệ.',
        ];
    }
}
