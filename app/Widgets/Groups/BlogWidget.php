<?php

namespace App\Widgets\Groups;

use App\Models\Post;
use App\Models\Taxonomy;
use App\Widgets\BaseWidget;

class BlogWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        $categories = [];
        $postsList = [];
        try {
            if (class_exists(Taxonomy::class)) {
                $categories = Taxonomy::withoutGlobalScopes()->pluck('name', 'id')->toArray();
            }
            if (class_exists(Post::class)) {
                $postsList = Post::withoutGlobalScopes()->where('post_type', 'post')->pluck('title', 'id')->toArray();
            }
        } catch (\Throwable $e) {
        }

        return [
            'name' => 'Blog Group',
            'description' => 'Khối bài viết / tin tức cập nhật',
            'category' => 'Blogs',
            'version' => '1.0.0',
            'icon' => 'newspaper',
            'group' => 'blog',
            'fields' => [
                [
                    'name' => 'section_title',
                    'label' => 'Tiêu đề khối Tin tức',
                    'type' => 'text',
                    'default' => 'Tin tức & Bài viết',
                ],
                [
                    'name' => 'section_subtitle',
                    'label' => 'Mô tả ngắn',
                    'type' => 'text',
                ],
                [
                    'name' => 'category_id',
                    'label' => 'Lọc bài viết theo Danh mục',
                    'type' => 'select',
                    'options' => ['' => '-- Tất cả danh mục tin tức --'] + $categories,
                    'help' => 'Chọn danh mục bài viết từ cơ sở dữ liệu',
                ],
                [
                    'name' => 'post_ids',
                    'label' => 'Chọn Bài viết cụ thể',
                    'type' => 'relationship',
                    'post_type' => 'post',
                    'multiple' => true,
                    'help' => 'Chọn một hoặc nhiều bài viết cụ thể để hiển thị ngay lập tức',
                ],
                [
                    'name' => 'limit',
                    'label' => 'Số lượng bài viết hiển thị',
                    'type' => 'number',
                    'default' => 6,
                ],
                [
                    'name' => 'layout_type',
                    'label' => 'Kiểu hiển thị (Layout)',
                    'type' => 'select',
                    'options' => [
                        'grid' => 'Lưới (Grid)',
                        'carousel' => 'Trượt (Carousel/Slider)',
                        'list' => 'Danh sách dọc (List)',
                    ],
                    'default' => 'carousel',
                ],
                [
                    'name' => 'columns_desktop',
                    'label' => 'Số cột trên Máy tính (Desktop)',
                    'type' => 'select',
                    'options' => [
                        '2' => '2 Cột',
                        '3' => '3 Cột',
                        '4' => '4 Cột',
                    ],
                    'default' => '3',
                ],
                [
                    'name' => 'columns_mobile',
                    'label' => 'Số cột trên Điện thoại (Mobile)',
                    'type' => 'select',
                    'options' => [
                        '1' => '1 Cột',
                        '2' => '2 Cột',
                    ],
                    'default' => '1',
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
        $postIds = $this->get('post_ids', []);
        if (! is_array($postIds) && ! empty($postIds)) {
            $postIds = [$postIds];
        }
        $categoryId = $this->get('category_id');
        $limit = (int) $this->get('limit', 6);
        $sectionTitle = $this->get('section_title', 'Tin tức & Bài viết');
        $sectionSubtitle = $this->get('section_subtitle', '');

        $query = Post::withoutGlobalScopes()->where('post_type', 'post');
        if (! empty($postIds)) {
            $query->whereIn('id', $postIds);
            if (count($postIds) > 1) {
                $query->orderByRaw('FIELD(id, '.implode(',', $postIds).')');
            }
        } elseif ($categoryId) {
            $query->whereHas('taxonomies', fn ($q) => $q->where('taxonomies.id', $categoryId));
        }

        if (empty($postIds)) {
            $query->orderBy('id', 'desc')->limit($limit);
        }

        $postsList = $query->get();
        if ($postsList->isEmpty()) {
            $postsList = Post::withoutGlobalScopes()->where('post_type', 'post')->orderBy('id', 'desc')->limit($limit)->get();
        }

        $posts = $postsList->map(function ($p) {
            $rawSummary = strip_tags($p->excerpt ?? $p->content ?? '');
            $cleanSummary = mb_convert_encoding(\Str::limit($rawSummary, 120), 'UTF-8', 'UTF-8');

            return [
                'id' => $p->id,
                'title' => mb_convert_encoding($p->title ?? '', 'UTF-8', 'UTF-8'),
                'image' => $p->featured_image ?? ($p->thumbnail ?? '/theme/images/blog/blog-01.jpg'),
                'date' => $p->created_at ? $p->created_at->format('d/m/Y') : '',
                'author' => 'Admin',
                'summary' => $cleanSummary,
                'link' => $p->slug ? url($p->slug) : '#',
            ];
        })->toArray();

        return [
            'section_title' => $sectionTitle,
            'section_subtitle' => $sectionSubtitle,
            'posts' => $posts,
            'slides' => $posts, // Fallback
        ];
    }
}
