<?php

namespace App\Widgets\Groups;

use App\Models\Post;
use App\Models\Taxonomy;
use App\Widgets\BaseWidget;

class ProductWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        $categories = [];
        $productsList = [];
        try {
            if (class_exists(Taxonomy::class)) {
                $categories = Taxonomy::pluck('name', 'id')->toArray();
            }
            if (class_exists(Post::class)) {
                $productsList = Post::where('post_type', 'product')->pluck('title', 'id')->toArray();
            }
        } catch (\Throwable $e) {
        }

        return [
            'name' => 'Product Group',
            'description' => 'Khối hiển thị danh sách sản phẩm nổi bật & mới nhất',
            'category' => 'E-Commerce',
            'version' => '1.0.0',
            'icon' => 'shopping-bag',
            'group' => 'product',
            'fields' => [
                [
                    'name' => 'section_title',
                    'label' => 'Tiêu đề khối Sản phẩm',
                    'type' => 'text',
                    'default' => 'Sản phẩm nổi bật',
                ],
                [
                    'name' => 'section_subtitle',
                    'label' => 'Mô tả ngắn',
                    'type' => 'text',
                ],
                [
                    'name' => 'category_id',
                    'label' => 'Lọc sản phẩm theo Danh mục',
                    'type' => 'select',
                    'options' => ['' => '-- Tất cả danh mục sản phẩm --'] + $categories,
                    'help' => 'Chọn danh mục để hiển thị sản phẩm tương ứng từ cơ sở dữ liệu',
                ],
                [
                    'name' => 'product_ids',
                    'label' => 'Chọn Sản phẩm cụ thể',
                    'type' => 'relationship',
                    'post_type' => 'product',
                    'multiple' => true,
                    'help' => 'Chọn một hoặc nhiều sản phẩm cụ thể để hiển thị ngay lập tức (sẽ ghi đè lọc theo danh mục)',
                ],
                [
                    'name' => 'limit',
                    'label' => 'Số lượng sản phẩm hiển thị',
                    'type' => 'number',
                    'default' => 8,
                ],
                [
                    'name' => 'layout_type',
                    'label' => 'Kiểu hiển thị (Layout)',
                    'type' => 'select',
                    'options' => [
                        'grid' => 'Lưới (Grid)',
                        'carousel' => 'Trượt (Carousel/Slider)',
                        'masonry' => 'Masonry',
                    ],
                    'default' => 'grid',
                ],
                [
                    'name' => 'columns_desktop',
                    'label' => 'Số cột trên Máy tính (Desktop)',
                    'type' => 'select',
                    'options' => [
                        '2' => '2 Cột',
                        '3' => '3 Cột',
                        '4' => '4 Cột',
                        '5' => '5 Cột',
                        '6' => '6 Cột',
                    ],
                    'default' => '4',
                ],
                [
                    'name' => 'columns_mobile',
                    'label' => 'Số cột trên Điện thoại (Mobile)',
                    'type' => 'select',
                    'options' => [
                        '1' => '1 Cột',
                        '2' => '2 Cột',
                    ],
                    'default' => '2',
                ],
                [
                    'name' => 'title_alignment',
                    'label' => 'Căn lề Tiêu đề',
                    'type' => 'select',
                    'options' => [
                        'left' => 'Trái',
                        'center' => 'Giữa',
                    ],
                    'default' => 'center',
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
        $productIds = $this->get('product_ids', []);
        if (! is_array($productIds) && ! empty($productIds)) {
            $productIds = [$productIds];
        }
        $categoryId = $this->get('category_id');
        $limit = (int) $this->get('limit', 8);
        $sectionTitle = $this->get('section_title', 'Sản phẩm nổi bật');
        $sectionSubtitle = $this->get('section_subtitle', '');

        $query = Post::where('post_type', 'product');
        if (! empty($productIds)) {
            $query->whereIn('id', $productIds);
            // Optional: Order by the specific IDs provided
            if (count($productIds) > 1) {
                $query->orderByRaw('FIELD(id, '.implode(',', $productIds).')');
            }
        } elseif ($categoryId) {
            $query->whereHas('taxonomies', function ($q) use ($categoryId) {
                $q->where('term_taxonomy_id', $categoryId);
            });
        }

        if (empty($productIds)) {
            $query->orderBy('order', 'asc')->orderBy('id', 'desc')->limit($limit);
        }

        $productsList = $query->get();

        if ($productsList->isEmpty()) {
            $productsList = Post::where('post_type', 'product')->orderBy('order', 'asc')->orderBy('id', 'desc')->limit($limit)->get();
        }

        $projectCode = request()->route('projectCode') ?? (request()->attributes->get('project')->code ?? null);

        $products = $productsList->map(function ($p) {
            $link = '#';
            if ($p->slug) {
                $link = locale_route('shop.show', $p->slug);
            }

            return [
                'id' => $p->id,
                'title' => $p->title,
                'image' => $p->featured_image ?? '/theme/images/products/pr-01.jpg',
                'price' => $p->price ? number_format((float) $p->price, 0, ',', '.').'đ' : 'Liên hệ',
                'sale_price' => $p->sale_price ? number_format((float) $p->sale_price, 0, ',', '.').'đ' : '',
                'link' => $link,
            ];
        })->toArray();

        return [
            'section_title' => $sectionTitle,
            'section_subtitle' => $sectionSubtitle,
            'products' => $products,
            'slides' => $products, // Fallback
        ];
    }
}
