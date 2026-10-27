<?php

namespace App\View\Components;

use App\Models\Menu;
use Illuminate\View\Component;

class MenuComponent extends Component
{
    public $menu;

    public $location;

    public function __construct($location = 'header')
    {
        $this->location = $location;
        $this->menu = null;
        try {
            $this->menu = Menu::where('location', $location)
                ->where('is_active', true)
                ->with(['items' => function ($query) {
                    $query->whereNull('parent_id')->with('children')->orderBy('order');
                }])
                ->first();
        } catch (\Exception $e) {
            // Bỏ qua lỗi nếu bảng menus không tồn tại
        }
    }

    public function render()
    {
        return view('components.menu');
    }

    public function renderItems($items, $level = 0)
    {
        if (! $items || $items->isEmpty()) {
            return '';
        }

        $html = '<ul class="menu-level-'.$level.($level === 0 ? ' main-menu' : ' sub-menu').'">';
        foreach ($items as $item) {
            $hasChildren = $item->children && $item->children->count() > 0;
            $html .= '<li class="menu-item'.($hasChildren ? ' has-children' : '').'">';

            // Get the actual URL
            $url = $item->url;
            if (! $url && $item->linkable) {
                switch ($item->linkable_type) {
                    case 'App\\Models\\Post':
                        $url = route('frontend.page', $item->linkable->slug ?? $item->linkable->id);
                        break;
                    case 'App\\Models\\ProductCategory':
                        $url = route('frontend.category', $item->linkable->slug ?? $item->linkable->id);
                        break;
                    default:
                        $url = '#';
                }
            }

            $html .= '<a href="'.($url ?: '#').'" target="'.($item->target ?: '_self').'" class="menu-link">'.$item->title.'</a>';

            if ($hasChildren) {
                $html .= $this->renderItems($item->children, $level + 1);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
