<?php

namespace App\Services\FieldTypes;

class ColorField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $colorValue = $value ?? $config['default'] ?? '#3B82F6';

        $isGradient = str_contains($colorValue, 'gradient');
        $initSolid = $isGradient ? '#ffffff' : $colorValue;
        $initGradient = $isGradient ? $colorValue : 'linear-gradient(to right, #3B82F6, #9333EA)';

        $typeString = $isGradient ? 'gradient' : 'solid';

        $fieldHtml = <<<HTML
        <div x-data="{ 
            type: '{$typeString}', 
            solidColor: '{$initSolid}',
            gradientVal: '{$initGradient}',
            get currentValue() {
                return this.type === 'solid' ? this.solidColor : this.gradientVal;
            }
        }" class="bg-gray-50 p-3 rounded-lg border border-gray-100">
            <div class="flex items-center gap-4 mb-3">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" x-model="type" value="solid" class="text-blue-600 form-radio border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Màu đơn (Solid)</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" x-model="type" value="gradient" class="text-blue-600 form-radio border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Hiệu ứng (Gradient)</span>
                </label>
            </div>
            
            <input type="hidden" name="{$name}" :value="currentValue">

            <div x-show="type === 'solid'" class="flex items-center gap-3">
                <input type="color" x-model="solidColor" class="w-12 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer bg-white">
                <input type="text" x-model="solidColor" class="w-32 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div x-show="type === 'gradient'" class="flex flex-col gap-2">
                <input type="text" x-model="gradientVal" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="linear-gradient(to right, #ff0000, #0000ff)">
                <div class="h-10 rounded-lg border border-gray-300 w-full shadow-inner" :style="'background: ' + gradientVal"></div>
                <p class="text-xs text-gray-500">Nhập mã CSS Gradient. Ví dụ: <code>linear-gradient(45deg, #3B82F6, #9333EA)</code></p>
            </div>
        </div>
        HTML;

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        // Remove strict hex validation to allow gradient strings like 'linear-gradient(...)'
        return parent::validate($value, $rules);
    }

    public static function getTypeName(): string
    {
        return 'color';
    }
}
