<?php

namespace App\Widgets\Groups;

use App\Models\ProductCategory;
use App\Models\Taxonomy;
use App\Widgets\BaseWidget;

class CategoryGridWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        $categories = [];
        try {
            if (class_exists(Taxonomy::class)) {
                $categories = Taxonomy::withoutGlobalScopes()->pluck('name', 'id')->toArray();
            }
            if (class_exists(ProductCategory::class)) {
                $categories += ProductCategory::withoutGlobalScopes()->pluck('name', 'id')->toArray();
            }
        } catch (\Throwable $e) {
        }

        return [
            'name' => 'Category Grid',
            'description' => 'Khối hiển thị danh sách Danh mục nổi bật',
            'category' => 'E-Commerce',
            'version' => '1.0.0',
            'icon' => 'grid',
            'group' => 'category_grid',
            'fields' => [
                [
                    'name' => 'section_title',
                    'label' => 'Tiêu đề khối',
                    'type' => 'text',
                    'default' => 'Danh mục Nổi bật',
                ],
                [
                    'name' => 'category_ids',
                    'label' => 'Chọn các Danh mục',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => $categories,
                    'help' => 'Chọn các danh mục muốn hiển thị',
                ],
                [
                    'name' => 'layout_type',
                    'label' => 'Kiểu hiển thị (Layout)',
                    'type' => 'select',
                    'options' => [
                        'grid' => 'Lưới (Grid)',
                        'carousel' => 'Trượt (Carousel/Slider)',
                    ],
                    'default' => 'grid',
                ],
                [
                    'name' => 'columns_desktop',
                    'label' => 'Số cột trên Máy tính (Desktop)',
                    'type' => 'select',
                    'options' => [
                        '3' => '3 Cột',
                        '4' => '4 Cột',
                        '5' => '5 Cột',
                        '6' => '6 Cột',
                    ],
                    'default' => '4',
                ],
            ],
            'settings' => [
                'cacheable' => false,
                'cache_duration' => 0,
            ],
        ];
    }

    /**
     * Prepare data for the Blade view
     */
    public function getViewData(): array
    {
        $categoryIds = $this->get('category_ids', []);
        if (! is_array($categoryIds) && ! empty($categoryIds)) {
            $categoryIds = [$categoryIds];
        }

        $categories = [];
        if (! empty($categoryIds) && class_exists(Taxonomy::class)) {
            $categories = Taxonomy::withoutGlobalScopes()
                ->whereIn('id', $categoryIds)
                ->get()
                ->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'image' => $c->image ?? '/theme/images/shop/cat-01.jpg',
                        'link' => $c->slug ? url($c->slug) : '#',
                    ];
                })->toArray();
        }

        // If no categories selected, we can return dummy or empty
        if (empty($categories)) {
            $categories = [
                ['name' => 'Fashion', 'image' => '/theme/images/shop/cat-01.jpg', 'link' => '#'],
                ['name' => 'Electronic', 'image' => '/theme/images/shop/cat-02.jpg', 'link' => '#'],
                ['name' => 'Furniture', 'image' => '/theme/images/shop/cat-03.jpg', 'link' => '#'],
                ['name' => 'Jewelry', 'image' => '/theme/images/shop/cat-04.jpg', 'link' => '#'],
            ];
        }

        return [
            'section_title' => $this->get('section_title', 'Danh mục Nổi bật'),
            'categories' => $categories,
        ];
    }
}
