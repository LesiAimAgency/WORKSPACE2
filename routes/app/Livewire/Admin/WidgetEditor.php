<?php

namespace App\Livewire\Admin;

use App\Models\Widget;
use App\Widgets\WidgetRegistry;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('cms.layouts.app')]
class WidgetEditor extends Component
{
    use WithFileUploads;

    public ?Widget $widget = null;

    // Widget info
    public string $name = '';

    public string $type = '';

    public string $area = 'homepage';

    public string $variant = 'default';

    public int $sort_order = 0;

    public bool $is_active = true;

    // Dynamic settings based on template fields
    public array $settings = [];

    // Available templates (both code-based and custom)
    public array $templates = [];

    // Fields from selected template
    public array $fields = [];

    // Current selected template info
    public ?array $template = null;

    // File uploads
    public array $uploads = [];

    protected $listeners = ['mediaSelected'];

    public function mount(?int $id = null, ?string $templateType = null): void
    {
        // Load all widgets from WidgetRegistry (includes both code-based and custom templates)
        $allWidgets = WidgetRegistry::all();

        // Convert to template format for the dropdown
        $this->templates = collect($allWidgets)->map(function ($widget) {
            return [
                'type' => $widget['type'],
                'name' => $widget['metadata']['name'] ?? $widget['type'],
                'category' => $widget['metadata']['category'] ?? 'general',
                'description' => $widget['metadata']['description'] ?? '',
                'is_custom' => $widget['metadata']['is_custom'] ?? false,
            ];
        })->sortBy(['category', 'name'])->values()->toArray();

        if ($id) {
            $this->widget = Widget::findOrFail($id);
            $this->name = $this->widget->name;
            $this->type = $this->widget->type;
            $this->area = $this->widget->area ?? 'homepage';
            $this->variant = $this->widget->variant ?? 'default';
            $this->sort_order = $this->widget->sort_order ?? 0;
            $this->is_active = $this->widget->is_active;
            $this->settings = $this->widget->settings ?? [];

            $this->loadTemplateFields();
        } elseif ($templateType) {
            $this->type = $templateType;
            $this->loadTemplateFields();
        }
    }

    public function updatedType(): void
    {
        $this->loadTemplateFields();
        $this->settings = [];

    }

    protected function loadTemplateFields(): void
    {
        if (empty($this->type)) {
            $this->template = null;
            $this->fields = [];

            return;
        }

        // First try to get config from WidgetRegistry (handles both code-based and custom)
        $config = WidgetRegistry::getConfig($this->type);

        if ($config) {
            $this->template = $config;
            $this->fields = $config['fields'] ?? [];

            // Initialize settings with defaults if empty
            if (empty($this->settings)) {
                foreach ($this->fields as $field) {
                    if (isset($field['default'])) {
                        $this->settings[$field['name']] = $field['default'];
                    }
                }
            }
        } else {
            $this->template = null;
            $this->fields = [];
        }
    }

    public function mediaSelected(string $fieldName, string $url): void
    {
        $this->settings[$fieldName] = $url;
    }

    public function addRepeaterItem(string $fieldName): void
    {
        if (! isset($this->settings[$fieldName])) {
            $this->settings[$fieldName] = [];
        }

        // Find field config to get sub-fields
        $fieldConfig = collect($this->fields)->firstWhere('name', $fieldName);
        $subFields = $fieldConfig['fields'] ?? [];

        $newItem = [];
        foreach ($subFields as $subField) {
            $newItem[$subField['name']] = $subField['default'] ?? '';
        }

        $this->settings[$fieldName][] = $newItem;
        $this->dispatch('repeater-updated', fieldName: $fieldName);
    }

    public function removeRepeaterItem(string $fieldName, int $index): void
    {
        if (isset($this->settings[$fieldName][$index])) {
            unset($this->settings[$fieldName][$index]);
            $this->settings[$fieldName] = array_values($this->settings[$fieldName]);
            $this->dispatch('repeater-updated', fieldName: $fieldName);
        }
    }

    public function reorderRepeater(string $fieldName, array $orderedIndices): void
    {
        if (! isset($this->settings[$fieldName]) || ! is_array($this->settings[$fieldName])) {
            return;
        }

        $currentItems = $this->settings[$fieldName];
        $reordered = [];
        foreach ($orderedIndices as $index) {
            if (array_key_exists($index, $currentItems)) {
                $reordered[] = $currentItems[$index];
            }
        }
        foreach ($currentItems as $idx => $item) {
            if (! in_array($idx, $orderedIndices, true)) {
                $reordered[] = $item;
            }
        }

        $this->settings[$fieldName] = $reordered;
        $this->dispatch('repeater-updated', fieldName: $fieldName);
    }

    public function removeGalleryImage(string $fieldName, int $index): void
    {
        if (isset($this->settings[$fieldName][$index])) {
            unset($this->settings[$fieldName][$index]);
            $this->settings[$fieldName] = array_values($this->settings[$fieldName]);
        }
    }

    public function addGalleryImage(string $fieldName, string $url): void
    {
        if (! isset($this->settings[$fieldName])) {
            $this->settings[$fieldName] = [];
        }
        $this->settings[$fieldName][] = $url;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string|max:100',
        ]);

        // Validate required fields
        foreach ($this->fields as $field) {
            if (($field['required'] ?? false) && empty($this->settings[$field['name']] ?? null)) {
                $this->addError('settings.'.$field['name'], "Field '{$field['label']}' là bắt buộc");

                return;
            }
        }

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'area' => $this->area,
            'variant' => $this->variant,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'settings' => $this->settings,
        ];

        if ($this->widget) {
            $this->widget->update($data);
            session()->flash('success', 'Widget đã được cập nhật!');
        } else {
            Widget::create($data);
            session()->flash('success', 'Widget đã được tạo!');
        }

        // Redirect based on context
        $projectCode = request()->route('projectCode');
        if ($projectCode) {
            $this->redirect(route('project.admin.widgets.index', $projectCode));
        } else {
            $this->redirect(route('cms.widgets.index'));
        }
    }

    public function getPreview(): string
    {
        if (empty($this->type)) {
            return '<div class="text-gray-500 text-center py-8">Chọn loại widget để xem preview</div>';
        }

        try {
            return WidgetRegistry::render($this->type, $this->settings, $this->variant);
        } catch (\Exception $e) {
            return '<div class="text-red-500 p-4">Preview Error: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }

    public function render()
    {
        return view('livewire.admin.widget-editor');
    }
}
