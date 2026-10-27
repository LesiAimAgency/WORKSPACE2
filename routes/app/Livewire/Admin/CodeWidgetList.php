<?php

namespace App\Livewire\Admin;

use App\Widgets\WidgetRegistry;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('cms.layouts.app')]
class CodeWidgetList extends Component
{
    public ?string $projectCode = null;

    public string $search = '';

    public string $categoryFilter = '';

    public function mount(): void
    {
        $this->projectCode = request()->route('projectCode');
    }

    public function getCodeWidgetsProperty(): array
    {
        $allWidgets = WidgetRegistry::all();

        // Filter only code-based widgets (not custom templates)
        $codeWidgets = collect($allWidgets)->filter(function ($widget) {
            return ! ($widget['metadata']['is_custom'] ?? false) && ! empty($widget['class']);
        });

        // Apply search filter
        if (! empty($this->search)) {
            $search = strtolower($this->search);
            $codeWidgets = $codeWidgets->filter(function ($widget) use ($search) {
                $name = strtolower($widget['metadata']['name'] ?? '');
                $type = strtolower($widget['type'] ?? '');
                $desc = strtolower($widget['metadata']['description'] ?? '');

                return str_contains($name, $search) || str_contains($type, $search) || str_contains($desc, $search);
            });
        }

        // Apply category filter
        if (! empty($this->categoryFilter)) {
            $codeWidgets = $codeWidgets->filter(function ($widget) {
                return ($widget['metadata']['category'] ?? '') === $this->categoryFilter;
            });
        }

        return $codeWidgets->groupBy(fn ($w) => $w['metadata']['category'] ?? 'general')->toArray();
    }

    public function getCategoriesProperty(): array
    {
        $allWidgets = WidgetRegistry::all();

        return collect($allWidgets)
            ->filter(fn ($w) => ! ($w['metadata']['is_custom'] ?? false) && ! empty($w['class']))
            ->pluck('metadata.category')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.code-widget-list', [
            'codeWidgets' => $this->codeWidgets,
            'categories' => $this->categories,
        ]);
    }
}
