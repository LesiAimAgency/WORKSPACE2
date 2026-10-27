<?php

namespace App\Livewire\Admin;

use App\Models\WidgetTemplate;
use App\Services\FieldTypeService;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('cms.layouts.app')]
class WidgetTemplateBuilder extends Component
{
    public ?WidgetTemplate $template = null;

    public ?string $projectCode = null;

    // Mode: 'custom' for database templates, 'code' for code-based widgets
    public string $mode = 'custom';

    public ?string $codeWidgetType = null;

    public ?string $codeWidgetClass = null;

    public string $name = '';

    public string $type = '';

    public string $category = 'general';

    public string $description = '';

    public string $icon = 'cube';

    public string $version = '1.0.0';

    public bool $is_active = true;

    public string $template_code = '';

    public string $template_css = '';

    public string $template_js = '';

    // For code-based widgets
    public string $phpPath = '';

    public string $viewPath = '';

    public string $jsonPath = '';

    public bool $hasJsonMetadata = false;

    public array $fields = [];

    public array $fieldTypes = [];

    public bool $showFieldModal = false;

    public int $editingFieldIndex = -1;

    public array $currentField = [];

    public string $activeTab = 'fields';

    public array $categories = [
        'general' => 'Chung',
        'hero' => 'Hero/Banner',
        'content' => 'Nội dung',
        'product' => 'Sản phẩm',
        'marketing' => 'Marketing',
        'layout' => 'Layout',
        'victorious' => 'Victorious Theme',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'description' => 'nullable|string|max:500',
    ];

    public function updatedName($value): void
    {
        if (! $this->template) {
            $this->type = $this->generateSlug($value);
        }
    }

    protected function generateSlug(string $text): string
    {
        $text = $this->removeVietnameseAccents($text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);

        return trim($text, '_');
    }

    protected function removeVietnameseAccents(string $str): string
    {
        $map = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a', 'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd', 'Đ' => 'D',
        ];

        return strtr($str, $map);
    }

    public function mount(?int $id = null, ?string $codeType = null): void
    {
        $this->projectCode = request()->route('projectCode');
        $this->loadFieldTypes();

        // Get codeType from route parameter if not passed directly
        // Livewire doesn't auto-inject route parameters, so we need to get them manually
        $codeType = $codeType ?? request()->route('codeType');

        // Debug logging
        \Log::info('WidgetTemplateBuilder mount', [
            'id' => $id,
            'codeType' => $codeType,
            'projectCode' => $this->projectCode,
            'route_codeType' => request()->route('codeType'),
            'full_url' => request()->fullUrl(),
        ]);

        // Check if editing a code-based widget
        if ($codeType) {
            $this->mode = 'code';
            $this->codeWidgetType = $codeType;
            $this->loadCodeBasedWidget($codeType);
        } elseif ($id) {
            $this->mode = 'custom';
            $this->template = WidgetTemplate::findOrFail($id);
            $this->name = $this->template->name;
            $this->type = $this->template->type;
            $this->category = $this->template->category;
            $this->description = $this->template->description ?? '';
            $this->icon = $this->template->icon ?? 'cube';
            $this->is_active = $this->template->is_active;
            $this->fields = $this->template->config_schema['fields'] ?? [];
            $this->loadCodeFromFiles();
        } else {
            $this->mode = 'custom';
            $this->template_code = $this->getDefaultTemplateCode();
        }

        $this->resetCurrentField();
    }

    protected function loadCodeBasedWidget(string $type): void
    {
        \Log::info('loadCodeBasedWidget called', ['type' => $type]);

        $this->codeWidgetClass = WidgetRegistry::get($type);

        \Log::info('Widget class found', ['class' => $this->codeWidgetClass]);

        if (! $this->codeWidgetClass || ! class_exists($this->codeWidgetClass)) {
            \Log::error('Widget class not found', ['type' => $type, 'class' => $this->codeWidgetClass]);
            session()->flash('error', "Widget type '{$type}' không tồn tại");

            return;
        }

        // Get paths
        $reflection = new \ReflectionClass($this->codeWidgetClass);
        $classDir = dirname($reflection->getFileName());
        $this->phpPath = $reflection->getFileName();
        $this->jsonPath = "{$classDir}/widget.json";

        // Check for widget.json metadata
        $this->hasJsonMetadata = File::exists($this->jsonPath);

        // Load metadata from registry
        $config = WidgetRegistry::getConfig($type);
        if ($config) {
            $this->name = $config['name'] ?? '';
            $this->description = $config['description'] ?? '';
            $this->category = $config['category'] ?? 'general';
            $this->version = $config['version'] ?? '1.0.0';
            $this->icon = $config['icon'] ?? 'cube';
            $this->fields = $config['fields'] ?? [];
            $this->type = $type;
        }

        // Load view code
        $phpContent = File::get($this->phpPath);
        if (preg_match("/view\(['\"]([^'\"]+)['\"]/", $phpContent, $matches)) {
            $viewName = $matches[1];
            $this->viewPath = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');
            if (File::exists($this->viewPath)) {
                $this->template_code = File::get($this->viewPath);
            }
        }

        // Load CSS/JS from view directory or class directory
        $viewDir = $this->viewPath ? dirname($this->viewPath) : $classDir;

        $cssPaths = ["{$viewDir}/style.css", "{$classDir}/style.css"];
        foreach ($cssPaths as $path) {
            if (File::exists($path)) {
                $this->template_css = File::get($path);
                break;
            }
        }

        $jsPaths = ["{$viewDir}/script.js", "{$classDir}/script.js"];
        foreach ($jsPaths as $path) {
            if (File::exists($path)) {
                $this->template_js = File::get($path);
                break;
            }
        }
    }

    protected function loadCodeFromFiles(): void
    {
        $dir = resource_path("views/widgets/custom/{$this->type}");
        if (File::exists("{$dir}/view.blade.php")) {
            $this->template_code = File::get("{$dir}/view.blade.php");
        }
        if (File::exists("{$dir}/style.css")) {
            $this->template_css = File::get("{$dir}/style.css");
        }
        if (File::exists("{$dir}/script.js")) {
            $this->template_js = File::get("{$dir}/script.js");
        }
    }

    protected function loadFieldTypes(): void
    {
        $service = new FieldTypeService;
        $info = $service->getFieldTypeInfo();

        // Convert to simple array format that Livewire can serialize
        $this->fieldTypes = [];
        foreach ($info as $key => $data) {
            $this->fieldTypes[$key] = [
                'name' => $data['name'] ?? $key,
                'description' => $data['description'] ?? '',
            ];
        }
    }

    protected function getDefaultTemplateCode(): string
    {
        return "<div class=\"widget-container p-4\">\n    {{-- Your widget code here --}}\n</div>";
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function resetCurrentField(): void
    {
        $this->currentField = [
            'name' => '', 'label' => '', 'type' => 'text', 'required' => false,
            'default' => '', 'help' => '', 'placeholder' => '', 'rows' => 4,
            'min' => null, 'max' => null, 'step' => 1, 'options' => [],
            'multiple' => false, 'return_format' => 'url', 'post_type' => 'product',
            'taxonomy' => 'category', 'layout' => 'table', 'button_label' => 'Add Row',
            'fields' => [],
        ];
    }

    public function openAddFieldModal(): void
    {
        $this->resetCurrentField();
        $this->editingFieldIndex = -1;
        $this->showFieldModal = true;
    }

    public function openEditFieldModal(int $index): void
    {
        $this->currentField = $this->fields[$index];
        $this->editingFieldIndex = $index;
        $this->showFieldModal = true;
    }

    public function closeFieldModal(): void
    {
        $this->showFieldModal = false;
        $this->resetCurrentField();
        $this->editingFieldIndex = -1;
    }

    public function saveField(): void
    {
        $this->validate([
            'currentField.name' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
            'currentField.label' => 'required|string|max:255',
            'currentField.type' => 'required|string',
        ]);

        $field = [
            'name' => $this->currentField['name'],
            'label' => $this->currentField['label'],
            'type' => $this->currentField['type'],
            'required' => $this->currentField['required'] ?? false,
            'default' => $this->currentField['default'] ?? '',
        ];

        if ($this->editingFieldIndex >= 0) {
            $this->fields[$this->editingFieldIndex] = $field;
        } else {
            $this->fields[] = $field;
        }
        $this->closeFieldModal();
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function moveFieldUp(int $index): void
    {
        if ($index > 0) {
            [$this->fields[$index - 1], $this->fields[$index]] = [$this->fields[$index], $this->fields[$index - 1]];
        }
    }

    public function moveFieldDown(int $index): void
    {
        if ($index < \count($this->fields) - 1) {
            [$this->fields[$index + 1], $this->fields[$index]] = [$this->fields[$index], $this->fields[$index + 1]];
        }
    }

    public function addSelectOption(): void
    {
        $this->currentField['options'][] = ['value' => '', 'label' => ''];
    }

    public function removeSelectOption(int $index): void
    {
        unset($this->currentField['options'][$index]);
        $this->currentField['options'] = array_values($this->currentField['options']);
    }

    public function addRepeatableField(): void
    {
        $this->currentField['fields'][] = ['name' => '', 'label' => '', 'type' => 'text'];
    }

    public function removeRepeatableField(int $index): void
    {
        unset($this->currentField['fields'][$index]);
        $this->currentField['fields'] = array_values($this->currentField['fields']);
    }

    public function save(): void
    {
        $this->validate();

        if ($this->mode === 'code') {
            $this->saveCodeBasedWidget();
        } else {
            $this->saveCustomTemplate();
        }
    }

    protected function saveCodeBasedWidget(): void
    {
        // Save view code
        if (! empty($this->viewPath) && ! empty($this->template_code)) {
            File::put($this->viewPath, $this->template_code);
        }

        // Save CSS
        if (! empty($this->template_css)) {
            $viewDir = $this->viewPath ? dirname($this->viewPath) : dirname($this->phpPath);
            $cssPath = "{$viewDir}/style.css";
            File::put($cssPath, $this->template_css);
        }

        // Save JS
        if (! empty($this->template_js)) {
            $viewDir = $this->viewPath ? dirname($this->viewPath) : dirname($this->phpPath);
            $jsPath = "{$viewDir}/script.js";
            File::put($jsPath, $this->template_js);
        }

        // Save metadata to widget.json
        $metadata = [
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'version' => $this->version,
            'icon' => $this->icon,
            'fields' => $this->fields,
            'variants' => ['default' => 'Default'],
        ];

        $jsonPath = $this->jsonPath ?: dirname($this->phpPath).'/widget.json';
        File::put($jsonPath, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        WidgetRegistry::clearCache();

        session()->flash('success', 'Code-based Widget đã được cập nhật!');

        if ($this->projectCode) {
            $this->redirect(route('project.admin.code-widgets.index', ['projectCode' => $this->projectCode]));
        } else {
            $this->redirect(route('cms.code-widgets.index'));
        }
    }

    protected function saveCustomTemplate(): void
    {
        // Create widget folder with files
        $dir = resource_path("views/widgets/custom/{$this->type}");
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put("{$dir}/view.blade.php", $this->template_code ?: $this->getDefaultTemplateCode());
        File::put("{$dir}/style.css", "/* Widget: {$this->name} */\n\n".$this->template_css);
        File::put("{$dir}/script.js", "/* Widget: {$this->name} */\n\n".$this->template_js);

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'category' => $this->category,
            'description' => $this->description,
            'icon' => $this->icon,
            'config_schema' => ['fields' => $this->fields],
            'default_settings' => [],
            'is_active' => $this->is_active,
        ];

        if ($this->template) {
            $this->template->update($data);
            session()->flash('success', 'Widget đã được cập nhật!');
        } else {
            WidgetTemplate::create($data);
            session()->flash('success', 'Widget đã được tạo!');
        }

        WidgetRegistry::clearCache();

        if ($this->projectCode) {
            $this->redirect(route('project.admin.code-widgets.index', ['projectCode' => $this->projectCode]));
        } else {
            $this->redirect(route('cms.code-widgets.index'));
        }
    }

    public function render()
    {
        return view('livewire.admin.widget-template-builder');
    }
}
