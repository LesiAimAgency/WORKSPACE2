<?php

namespace App\Livewire\Admin;

use App\Services\FieldTypeService;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('cms.layouts.app')]
class CodeWidgetEditor extends Component
{
    public string $widgetType = '';

    public string $widgetClass = '';

    public ?string $projectCode = null;

    // Widget metadata
    public string $name = '';

    public string $description = '';

    public string $category = '';

    public string $version = '';

    public string $icon = '';

    // Fields configuration
    public array $fields = [];

    // Code content
    public string $viewCode = '';

    public string $cssCode = '';

    public string $jsCode = '';

    public string $phpCode = '';

    // UI state
    public string $activeTab = 'fields';

    public bool $showFieldModal = false;

    public int $editingFieldIndex = -1;

    public array $currentField = [];

    public array $fieldTypes = [];

    // Paths
    public string $viewPath = '';

    public string $cssPath = '';

    public string $jsPath = '';

    public string $phpPath = '';

    public string $jsonPath = '';

    public bool $hasJsonMetadata = false;

    public bool $isReadOnly = false;

    protected array $categories = [
        'general' => 'Chung',
        'hero' => 'Hero/Banner',
        'content' => 'Nội dung',
        'product' => 'Sản phẩm',
        'marketing' => 'Marketing',
        'layout' => 'Layout',
        'victorious' => 'Victorious Theme',
    ];

    public function mount(string $type): void
    {
        $this->projectCode = request()->route('projectCode');
        $this->widgetType = $type;

        // Get widget class from registry
        $this->widgetClass = WidgetRegistry::get($type);

        if (! $this->widgetClass) {
            session()->flash('error', "Widget type '{$type}' không tồn tại hoặc không phải Code-based Widget");

            return;
        }

        $this->loadFieldTypes();
        $this->loadWidgetData();
        $this->resetCurrentField();
    }

    protected function loadFieldTypes(): void
    {
        $service = new FieldTypeService;
        $info = $service->getFieldTypeInfo();

        $this->fieldTypes = [];
        foreach ($info as $key => $data) {
            $this->fieldTypes[$key] = [
                'name' => $data['name'] ?? $key,
                'description' => $data['description'] ?? '',
            ];
        }
    }

    protected function loadWidgetData(): void
    {
        if (! $this->widgetClass || ! class_exists($this->widgetClass)) {
            return;
        }

        // Get paths
        $reflection = new \ReflectionClass($this->widgetClass);
        $classDir = dirname($reflection->getFileName());
        $this->phpPath = $reflection->getFileName();
        $this->jsonPath = "{$classDir}/widget.json";

        // Check for widget.json metadata
        $this->hasJsonMetadata = File::exists($this->jsonPath);

        // Load metadata
        $config = WidgetRegistry::getConfig($this->widgetType);
        if ($config) {
            $this->name = $config['name'] ?? '';
            $this->description = $config['description'] ?? '';
            $this->category = $config['category'] ?? 'general';
            $this->version = $config['version'] ?? '1.0.0';
            $this->icon = $config['icon'] ?? 'cube';
            $this->fields = $config['fields'] ?? [];
        }

        // Load PHP class code
        if (File::exists($this->phpPath)) {
            $this->phpCode = File::get($this->phpPath);
        }

        // Find view path from render method or convention
        $this->viewPath = $this->findViewPath();
        if ($this->viewPath && File::exists($this->viewPath)) {
            $this->viewCode = File::get($this->viewPath);
        }

        // Find CSS/JS paths (same directory as view or widget class)
        $this->findAssetPaths();
    }

    protected function findViewPath(): string
    {
        // Try to extract view name from render() method
        if (preg_match("/view\(['\"]([^'\"]+)['\"]/", $this->phpCode, $matches)) {
            $viewName = $matches[1];
            $viewPath = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');
            if (File::exists($viewPath)) {
                return $viewPath;
            }
        }

        // Fallback: check common locations
        $possiblePaths = [
            resource_path("views/widgets/{$this->widgetType}.blade.php"),
            resource_path('views/widgets/'.str_replace('_', '/', $this->widgetType).'.blade.php'),
        ];

        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return '';
    }

    protected function findAssetPaths(): void
    {
        $viewDir = $this->viewPath ? dirname($this->viewPath) : '';
        $classDir = dirname($this->phpPath);

        // Check for CSS
        $cssPaths = [
            "{$viewDir}/style.css",
            "{$viewDir}/{$this->widgetType}.css",
            "{$classDir}/style.css",
        ];
        foreach ($cssPaths as $path) {
            if (File::exists($path)) {
                $this->cssPath = $path;
                $this->cssCode = File::get($path);
                break;
            }
        }

        // Check for JS
        $jsPaths = [
            "{$viewDir}/script.js",
            "{$viewDir}/{$this->widgetType}.js",
            "{$classDir}/script.js",
        ];
        foreach ($jsPaths as $path) {
            if (File::exists($path)) {
                $this->jsPath = $path;
                $this->jsCode = File::get($path);
                break;
            }
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function resetCurrentField(): void
    {
        $this->currentField = [
            'name' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'default' => '',
            'help' => '',
            'placeholder' => '',
            'options' => [],
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

        // Add type-specific properties
        if (! empty($this->currentField['help'])) {
            $field['help'] = $this->currentField['help'];
        }
        if (! empty($this->currentField['placeholder'])) {
            $field['placeholder'] = $this->currentField['placeholder'];
        }
        if (! empty($this->currentField['options'])) {
            $field['options'] = $this->currentField['options'];
        }
        if (! empty($this->currentField['fields'])) {
            $field['fields'] = $this->currentField['fields'];
        }

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
        // Save widget.json metadata
        $this->saveMetadata();

        // Save view code
        $this->saveViewCode();

        // Save CSS
        $this->saveCssCode();

        // Save JS
        $this->saveJsCode();

        // Clear widget cache
        WidgetRegistry::clearCache();

        session()->flash('success', 'Widget đã được cập nhật thành công!');
    }

    protected function saveMetadata(): void
    {
        $metadata = [
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'version' => $this->version,
            'icon' => $this->icon,
            'fields' => $this->fields,
            'variants' => ['default' => 'Default'],
        ];

        // If widget.json exists, update it
        if ($this->hasJsonMetadata || ! empty($this->jsonPath)) {
            $jsonPath = $this->jsonPath ?: dirname($this->phpPath).'/widget.json';
            File::put($jsonPath, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->hasJsonMetadata = true;
        } else {
            // Update getConfig() in PHP class
            $this->updatePhpConfig();
        }
    }

    protected function updatePhpConfig(): void
    {
        // Generate new getConfig() method content
        $fieldsCode = $this->generateFieldsArrayCode();

        $newConfig = <<<PHP
    public static function getConfig(): array
    {
        return [
            'name' => '{$this->name}',
            'description' => '{$this->description}',
            'category' => '{$this->category}',
            'version' => '{$this->version}',
            'icon' => '{$this->icon}',
            'fields' => {$fieldsCode},
            'variants' => ['default' => 'Default'],
        ];
    }
PHP;

        // Replace existing getConfig() method
        $pattern = '/public\s+static\s+function\s+getConfig\s*\(\s*\)\s*:\s*array\s*\{[^}]+(?:\{[^}]*\}[^}]*)*\}/s';

        if (preg_match($pattern, $this->phpCode)) {
            $this->phpCode = preg_replace($pattern, $newConfig, $this->phpCode);
            File::put($this->phpPath, $this->phpCode);
        }
    }

    protected function generateFieldsArrayCode(): string
    {
        $lines = ['['];
        foreach ($this->fields as $field) {
            $fieldStr = "                ['name' => '{$field['name']}', 'label' => '{$field['label']}', 'type' => '{$field['type']}'";
            if (! empty($field['required'])) {
                $fieldStr .= ", 'required' => true";
            }
            if (! empty($field['default'])) {
                $default = addslashes($field['default']);
                $fieldStr .= ", 'default' => '{$default}'";
            }
            $fieldStr .= '],';
            $lines[] = $fieldStr;
        }
        $lines[] = '            ]';

        return implode("\n", $lines);
    }

    protected function saveViewCode(): void
    {
        if (! empty($this->viewPath) && ! empty($this->viewCode)) {
            File::put($this->viewPath, $this->viewCode);
        }
    }

    protected function saveCssCode(): void
    {
        if (! empty($this->cssCode)) {
            $cssPath = $this->cssPath ?: dirname($this->viewPath ?: $this->phpPath).'/style.css';
            File::put($cssPath, $this->cssCode);
            $this->cssPath = $cssPath;
        }
    }

    protected function saveJsCode(): void
    {
        if (! empty($this->jsCode)) {
            $jsPath = $this->jsPath ?: dirname($this->viewPath ?: $this->phpPath).'/script.js';
            File::put($jsPath, $this->jsCode);
            $this->jsPath = $jsPath;
        }
    }

    public function getPreview(): string
    {
        try {
            // Create temporary settings with defaults
            $settings = [];
            foreach ($this->fields as $field) {
                $settings[$field['name']] = $field['default'] ?? '';
            }

            return WidgetRegistry::render($this->widgetType, $settings);
        } catch (\Exception $e) {
            return '<div class="text-red-500 p-4">Preview Error: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }

    public function render()
    {
        return view('livewire.admin.code-widget-editor', [
            'categories' => $this->categories,
        ]);
    }
}
