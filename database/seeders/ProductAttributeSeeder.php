<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultAttrs = [
            [
                'name' => 'Cấp điện áp',
                'slug' => 'cap-dien-ap',
                'type' => 'button',
                'description' => 'Điện áp định mức của thiết bị điện',
                'values' => [
                    ['name' => '12kV'],
                    ['name' => '24kV'],
                    ['name' => '35kV'],
                    ['name' => '110kV'],
                    ['name' => '220kV'],
                ],
            ],
            [
                'name' => 'Dòng điện định mức',
                'slug' => 'dong-dien-dinh-muc',
                'type' => 'button',
                'description' => 'Cường độ dòng điện định mức',
                'values' => [
                    ['name' => '100A'],
                    ['name' => '200A'],
                    ['name' => '400A'],
                    ['name' => '630A'],
                    ['name' => '1250A'],
                ],
            ],
            [
                'name' => 'Màu sắc',
                'slug' => 'mau-sac',
                'type' => 'color',
                'description' => 'Màu sắc nhận diện sản phẩm',
                'values' => [
                    ['name' => 'Đỏ', 'color_code' => '#EF4444'],
                    ['name' => 'Xanh dương', 'color_code' => '#3B82F6'],
                    ['name' => 'Xanh lá', 'color_code' => '#10B981'],
                    ['name' => 'Vàng', 'color_code' => '#F59E0B'],
                    ['name' => 'Trắng', 'color_code' => '#FFFFFF'],
                    ['name' => 'Đen', 'color_code' => '#1F2937'],
                    ['name' => 'Ghi xám', 'color_code' => '#6B7280'],
                ],
            ],
            [
                'name' => 'Công suất',
                'slug' => 'cong-suat',
                'type' => 'select',
                'description' => 'Công suất máy biến áp hoặc thiết bị tải',
                'values' => [
                    ['name' => '50kVA'],
                    ['name' => '100kVA'],
                    ['name' => '250kVA'],
                    ['name' => '400kVA'],
                    ['name' => '560kVA'],
                    ['name' => '630kVA'],
                    ['name' => '1000kVA'],
                ],
            ],
            [
                'name' => 'Kích thước',
                'slug' => 'kich-thuoc',
                'type' => 'button',
                'description' => 'Kích cỡ hoặc quy cách đóng gói',
                'values' => [
                    ['name' => 'Tiêu chuẩn'],
                    ['name' => 'S'],
                    ['name' => 'M'],
                    ['name' => 'L'],
                    ['name' => 'XL'],
                    ['name' => 'XXL'],
                ],
            ],
            [
                'name' => 'Chất liệu cách điện',
                'slug' => 'chat-lieu-cach-dien',
                'type' => 'select',
                'description' => 'Vật liệu chế tạo bề mặt cách điện',
                'values' => [
                    ['name' => 'Polymer (Silicone Rubber)'],
                    ['name' => 'Sứ cách điện'],
                    ['name' => 'Composite'],
                    ['name' => 'Cao su EPDM'],
                ],
            ],
        ];

        foreach ($defaultAttrs as $idx => $attr) {
            $parent = Taxonomy::firstOrCreate(
                ['slug' => $attr['slug'], 'taxonomy' => 'product_attribute'],
                [
                    'name' => $attr['name'],
                    'description' => $attr['description'],
                    'parent_id' => null,
                    'status' => 'published',
                    'order' => $idx,
                    'meta_data' => ['type' => $attr['type']],
                ]
            );

            foreach ($attr['values'] as $vIdx => $val) {
                $vSlug = Str::slug($val['name']);
                Taxonomy::firstOrCreate(
                    ['slug' => $attr['slug'].'-'.$vSlug, 'taxonomy' => 'product_attribute', 'parent_id' => $parent->id],
                    [
                        'name' => $val['name'],
                        'status' => 'published',
                        'order' => $vIdx,
                        'meta_data' => ['color_code' => $val['color_code'] ?? null],
                    ]
                );
            }
        }
    }
}
