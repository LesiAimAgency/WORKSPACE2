<?php

namespace App\Services;

use App\Contracts\FieldTypeInterface;
use App\Services\FieldTypes\CheckboxField;
use App\Services\FieldTypes\CkeditorField;
use App\Services\FieldTypes\ColorField;
use App\Services\FieldTypes\DateField;
use App\Services\FieldTypes\DateTimeField;
use App\Services\FieldTypes\EmailField;
use App\Services\FieldTypes\GalleryField;
use App\Services\FieldTypes\ImageField;
use App\Services\FieldTypes\NumberField;
use App\Services\FieldTypes\PostObjectField;
use App\Services\FieldTypes\RadioField;
use App\Services\FieldTypes\RangeField;
use App\Services\FieldTypes\RelationshipField;
use App\Services\FieldTypes\RepeatableField;
use App\Services\FieldTypes\SelectField;
use App\Services\FieldTypes\TaxonomyField;
use App\Services\FieldTypes\TextareaField;
use App\Services\FieldTypes\TextField;
use App\Services\FieldTypes\UrlField;
use App\Services\FieldTypes\VideoField;
use App\Services\FieldTypes\WysiwygField;

class FieldTypeService
{
    protected array $fieldTypes = [];

    public function __construct()
    {
        $this->registerDefaultFieldTypes();
    }

    /**
     * Register default field types
     */
    protected function registerDefaultFieldTypes(): void
    {
        // Basic fields
        $this->register(new TextField);
        $this->register(new TextareaField);
        $this->register(new WysiwygField);
        $this->register(new CkeditorField);
        $this->register(new NumberField);
        $this->register(new EmailField);
        $this->register(new UrlField);
        $this->register(new DateField);
        $this->register(new DateTimeField);

        // Choice fields
        $this->register(new SelectField);
        $this->register(new CheckboxField);
        $this->register(new RadioField);

        // Media fields
        $this->register(new ImageField);
        $this->register(new VideoField);
        $this->register(new GalleryField);
        $this->register(new ColorField);

        // Relational fields (ACF-like)
        $this->register(new RelationshipField);
        $this->register(new PostObjectField);
        $this->register(new TaxonomyField);

        // Layout fields
        $this->register(new RepeatableField);
        $this->register(new RangeField);
    }

    /**
     * Register a field type
     */
    public function register(FieldTypeInterface $fieldType): void
    {
        $this->fieldTypes[$fieldType::getTypeName()] = $fieldType;
    }

    /**
     * Get field type instance
     */
    public function get(string $type): ?FieldTypeInterface
    {
        if ($type === 'datetime-local' || $type === 'picktime') {
            $type = 'datetime';
        }

        if ($type === 'repeater') {
            $type = 'repeatable';
        }

        return $this->fieldTypes[$type] ?? null;
    }

    /**
     * Check if field type exists
     */
    public function exists(string $type): bool
    {
        if ($type === 'datetime-local' || $type === 'picktime') {
            $type = 'datetime';
        }

        if ($type === 'repeater') {
            $type = 'repeatable';
        }

        return isset($this->fieldTypes[$type]);
    }

    /**
     * Get all registered field types
     */
    public function getAll(): array
    {
        return $this->fieldTypes;
    }

    /**
     * Get field type names
     */
    public function getTypeNames(): array
    {
        return array_keys($this->fieldTypes);
    }

    /**
     * Render field HTML
     */
    public function renderField(array $fieldConfig, mixed $value = null): string
    {
        $type = $fieldConfig['type'] ?? 'text';
        $fieldType = $this->get($type);

        if (! $fieldType) {
            throw new \InvalidArgumentException("Field type '{$type}' not found");
        }

        return $fieldType->render($fieldConfig, $value);
    }

    /**
     * Validate field value
     */
    public function validateField(array $fieldConfig, mixed $value): bool
    {
        $type = $fieldConfig['type'] ?? 'text';
        $fieldType = $this->get($type);

        if (! $fieldType) {
            // Unknown field type - skip validation
            return true;
        }

        $rules = [];
        if (isset($fieldConfig['validation'])) {
            $rules = explode('|', $fieldConfig['validation']);
        }

        // Set config for field types that need it (like select)
        if (method_exists($fieldType, 'setConfig')) {
            $fieldType->setConfig($fieldConfig);
        }

        return $fieldType->validate($value, $rules);
    }

    /**
     * Transform field value
     */
    public function transformField(array $fieldConfig, mixed $value): mixed
    {
        $type = $fieldConfig['type'] ?? 'text';
        $fieldType = $this->get($type);

        if (! $fieldType) {
            return $value;
        }

        return $fieldType->transform($value);
    }

    /**
     * Render form from field configurations with Tabbed UI
     */
    public function renderForm(array $fields, array $values = []): string
    {
        $contentHtml = '';

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldValue = $values[$fieldName] ?? null;

            try {
                $contentHtml .= $this->renderField($field, $fieldValue);
            } catch (\Exception $e) {
                \Log::error("Field render error for {$fieldName}: ".$e->getMessage());
                $contentHtml .= '<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">';
                $contentHtml .= "<p class=\"text-red-600 text-sm\">Error rendering field '{$fieldName}': ".htmlspecialchars($e->getMessage()).'</p>';
                $contentHtml .= '</div>';
            }
        }

        $styleHtml = $this->renderStyleAndAdvancedTab($values);

        return '
        <div x-data="{ activeTab: \'content\' }" class="widget-config-tabs">
            <div class="flex border-b border-gray-200 mb-4 bg-gray-100 rounded-xl p-1">
                <button type="button" 
                        @click="activeTab = \'content\'" 
                        :class="activeTab === \'content\' ? \'bg-white text-blue-600 shadow-sm font-semibold\' : \'text-gray-600 hover:text-gray-900\'" 
                        class="flex-1 py-2 px-3 text-xs rounded-lg transition flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Nội dung Widget
                </button>
                <button type="button" 
                        @click="activeTab = \'style\'" 
                        :class="activeTab === \'style\' ? \'bg-white text-blue-600 shadow-sm font-semibold\' : \'text-gray-600 hover:text-gray-900\'" 
                        class="flex-1 py-2 px-3 text-xs rounded-lg transition flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Kiểu dáng & Nâng cao
                </button>
            </div>

            <div x-show="activeTab === \'content\'" class="space-y-4">
                '.$contentHtml.'
            </div>

            <div x-show="activeTab === \'style\'" class="space-y-5">
                '.$styleHtml.'
            </div>
        </div>';
    }

    /**
     * Render universal Style & Advanced tab fields (Padding/Margin box, Typography, Code Injections, Background)
     */
    public function renderStyleAndAdvancedTab(array $values = []): string
    {
        $v = function ($key, $default = '') use ($values) {
            return htmlspecialchars((string) ($values[$key] ?? $default), ENT_QUOTES);
        };

        $titleFontWeight = $values['title_font_weight'] ?? '';
        $descFontWeight = $values['description_font_weight'] ?? '';
        $bgType = $values['bg_type'] ?? 'color';

        return '
        <!-- Khoảng cách Margin & Padding -->
        <div class="bg-white border rounded-xl p-4 shadow-sm">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                Khoảng cách (Margin & Padding)
            </h4>

            <!-- CSS Box Model Diagram -->
            <div class="relative bg-orange-50/60 border-2 border-dashed border-orange-300 rounded-xl p-3 text-center mx-auto max-w-[280px]">
                <!-- MARGIN label -->
                <span class="absolute top-1 left-2 text-[9px] font-bold text-orange-500 uppercase tracking-widest">MARGIN</span>

                <!-- Margin Top -->
                <div class="flex justify-center mb-2 mt-2">
                    <input type="text" name="margin_top" value="'.$v('margin_top').'" placeholder="0" class="w-12 text-center text-[11px] py-1 border border-orange-200 rounded bg-white font-mono focus:ring-1 focus:ring-orange-400 focus:border-transparent" title="Margin Top">
                </div>

                <!-- Middle Row: Margin Left → Padding Box → Margin Right -->
                <div class="flex items-center justify-center gap-2">
                    <!-- Margin Left -->
                    <div class="flex-shrink-0">
                        <input type="text" name="margin_left" value="'.$v('margin_left').'" placeholder="0" class="w-12 text-center text-[11px] py-1 border border-orange-200 rounded bg-white font-mono focus:ring-1 focus:ring-orange-400 focus:border-transparent" title="Margin Left">
                    </div>

                    <!-- PADDING Box (inner) -->
                    <div class="flex-1 bg-green-50/70 border-2 border-dashed border-green-300 rounded-lg p-2 relative">
                        <span class="absolute top-0.5 left-1.5 text-[8px] font-bold text-green-600 uppercase tracking-widest">PADDING</span>

                        <!-- Padding Top -->
                        <div class="flex justify-center mb-1.5 mt-2">
                            <input type="text" name="padding_top" value="'.$v('padding_top').'" placeholder="0" class="w-10 text-center text-[10px] py-0.5 border border-green-200 rounded bg-white font-mono focus:ring-1 focus:ring-green-400 focus:border-transparent" title="Padding Top">
                        </div>

                        <!-- Padding Left + Content + Padding Right -->
                        <div class="flex items-center justify-center gap-1.5">
                            <input type="text" name="padding_left" value="'.$v('padding_left').'" placeholder="0" class="w-10 text-center text-[10px] py-0.5 border border-green-200 rounded bg-white font-mono flex-shrink-0 focus:ring-1 focus:ring-green-400 focus:border-transparent" title="Padding Left">
                            <div class="flex-1 bg-blue-50 border border-blue-200 rounded py-2 px-1">
                                <span class="text-[9px] font-semibold text-blue-500 uppercase tracking-wider">Content</span>
                            </div>
                            <input type="text" name="padding_right" value="'.$v('padding_right').'" placeholder="0" class="w-10 text-center text-[10px] py-0.5 border border-green-200 rounded bg-white font-mono flex-shrink-0 focus:ring-1 focus:ring-green-400 focus:border-transparent" title="Padding Right">
                        </div>

                        <!-- Padding Bottom -->
                        <div class="flex justify-center mt-1.5">
                            <input type="text" name="padding_bottom" value="'.$v('padding_bottom').'" placeholder="0" class="w-10 text-center text-[10px] py-0.5 border border-green-200 rounded bg-white font-mono focus:ring-1 focus:ring-green-400 focus:border-transparent" title="Padding Bottom">
                        </div>
                    </div>

                    <!-- Margin Right -->
                    <div class="flex-shrink-0">
                        <input type="text" name="margin_right" value="'.$v('margin_right').'" placeholder="0" class="w-12 text-center text-[11px] py-1 border border-orange-200 rounded bg-white font-mono focus:ring-1 focus:ring-orange-400 focus:border-transparent" title="Margin Right">
                    </div>
                </div>

                <!-- Margin Bottom -->
                <div class="flex justify-center mt-2">
                    <input type="text" name="margin_bottom" value="'.$v('margin_bottom').'" placeholder="0" class="w-12 text-center text-[11px] py-1 border border-orange-200 rounded bg-white font-mono focus:ring-1 focus:ring-orange-400 focus:border-transparent" title="Margin Bottom">
                </div>
            </div>

            <p class="text-[10px] text-gray-400 mt-2 text-center">Nhập giá trị CSS: 10px, 1rem, 5%, auto...</p>
        </div>


        <!-- Kiểu chữ Tiêu đề & Mô tả (Typography & Font Weight) -->
        <div class="bg-white border rounded-xl p-4 shadow-sm space-y-4">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Kiểu chữ Tiêu đề & Mô tả
            </h4>

            <div class="p-3 bg-gray-50 rounded-lg space-y-2">
                <span class="text-xs font-semibold text-gray-800 block">Tiêu đề (Title)</span>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] text-gray-500 block mb-1">Độ đậm (Font Weight)</label>
                        <select name="title_font_weight" class="w-full text-xs py-1.5 px-2 border rounded bg-white">
                            <option value="">Mặc định</option>
                            <option value="font-normal"'.($titleFontWeight === 'font-normal' ? ' selected' : '').'>Normal (400)</option>
                            <option value="font-medium"'.($titleFontWeight === 'font-medium' ? ' selected' : '').'>Medium (500)</option>
                            <option value="font-semibold"'.($titleFontWeight === 'font-semibold' ? ' selected' : '').'>SemiBold (600)</option>
                            <option value="font-bold"'.($titleFontWeight === 'font-bold' ? ' selected' : '').'>Bold (700)</option>
                            <option value="font-extrabold"'.($titleFontWeight === 'font-extrabold' ? ' selected' : '').'>ExtraBold (800)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-gray-500 block mb-1">Màu chữ (Title Color)</label>
                        <input type="color" name="title_color" value="'.$v('title_color', '#000000').'" class="w-full h-8 p-0.5 border rounded cursor-pointer">
                    </div>
                </div>
            </div>

            <div class="p-3 bg-gray-50 rounded-lg space-y-2">
                <span class="text-xs font-semibold text-gray-800 block">Mô tả (Description/Subtitle)</span>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] text-gray-500 block mb-1">Độ đậm (Font Weight)</label>
                        <select name="description_font_weight" class="w-full text-xs py-1.5 px-2 border rounded bg-white">
                            <option value="">Mặc định</option>
                            <option value="font-light"'.($descFontWeight === 'font-light' ? ' selected' : '').'>Light (300)</option>
                            <option value="font-normal"'.($descFontWeight === 'font-normal' ? ' selected' : '').'>Normal (400)</option>
                            <option value="font-medium"'.($descFontWeight === 'font-medium' ? ' selected' : '').'>Medium (500)</option>
                            <option value="font-semibold"'.($descFontWeight === 'font-semibold' ? ' selected' : '').'>SemiBold (600)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-gray-500 block mb-1">Màu chữ (Description Color)</label>
                        <input type="color" name="description_color" value="'.$v('description_color', '#4b5563').'" class="w-full h-8 p-0.5 border rounded cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        <!-- Mã chèn tùy chỉnh (Code Injection) -->
        <div class="bg-white border rounded-xl p-4 shadow-sm space-y-3">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                Mã chèn tùy chỉnh (Code Injection)
            </h4>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Body Code (Mã chèn sau &lt;body&gt;)</label>
                <textarea name="body_code" rows="3" class="w-full p-2 text-xs font-mono border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500" placeholder="Mã HTML/JS chèn sau <body>">'.$v('body_code').'</textarea>
                <p class="text-[10px] text-gray-400 mt-0.5">Mã sẽ được chèn ngay sau thẻ mở &lt;body&gt; của widget.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Footer Code (Mã chèn trước &lt;/body&gt;)</label>
                <textarea name="footer_code" rows="3" class="w-full p-2 text-xs font-mono border rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500" placeholder="Mã HTML/JS chèn trước </body>">'.$v('footer_code').'</textarea>
                <p class="text-[10px] text-gray-400 mt-0.5">Mã sẽ được chèn ngay trước thẻ đóng &lt;/body&gt; của widget.</p>
            </div>
        </div>

        </div>';
    }

    /**
     * Validate form data against field configurations
     */
    public function validateForm(array $fields, array $data): array
    {
        $errors = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldValue = $data[$fieldName] ?? null;

            // Check required fields
            if (($field['required'] ?? false) && empty($fieldValue)) {
                $errors[$fieldName] = "Field '{$field['label']}' is required";

                continue;
            }

            // Skip validation for empty non-required fields
            if (empty($fieldValue)) {
                continue;
            }

            // Validate field value
            if (! $this->validateField($field, $fieldValue)) {
                $errors[$fieldName] = "Field '{$field['label']}' has invalid value";
            }
        }

        return $errors;
    }

    /**
     * Transform form data
     */
    public function transformFormData(array $fields, array $data): array
    {
        $transformed = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldValue = $data[$fieldName] ?? null;

            if ($fieldValue !== null) {
                $transformed[$fieldName] = $this->transformField($field, $fieldValue);
            }
        }

        return $transformed;
    }

    /**
     * Get field type information
     */
    public function getFieldTypeInfo(): array
    {
        $info = [];

        foreach ($this->fieldTypes as $type => $fieldType) {
            $info[$type] = [
                'name' => $type,
                'class' => get_class($fieldType),
                'description' => $this->getFieldTypeDescription($type),
            ];
        }

        return $info;
    }

    /**
     * Get field type description
     */
    protected function getFieldTypeDescription(string $type): string
    {
        $descriptions = [
            'text' => 'Single line text input',
            'textarea' => 'Multi-line text input',
            'wysiwyg' => 'Rich text editor (WYSIWYG)',
            'select' => 'Dropdown selection',
            'checkbox' => 'Boolean checkbox',
            'image' => 'Image file upload',
            'video' => 'Video file upload',
            'gallery' => 'Multiple image gallery',
            'repeatable' => 'Repeatable group of fields',
            'url' => 'URL input with validation',
            'number' => 'Numeric input',
            'email' => 'Email input with validation',
            'date' => 'Date picker',
            'color' => 'Color picker',
            'range' => 'Range slider',
            'relationship' => 'Link to Products/Posts (multiple)',
            'post_object' => 'Link to single Product/Post',
            'taxonomy' => 'Select Category/Brand',
        ];

        return $descriptions[$type] ?? 'Custom field type';
    }
}
