<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Daiphuc68MasterSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $authorId = $user ? $user->id : 1;

        // 1. PROJECT DAIPHUC68
        $project = Project::firstOrCreate(
            ['code' => 'daiphuc68'],
            [
                'name' => 'Đại Phúc 68',
                'domain' => 'localhost',
                'features' => ['theme' => 'daiphuc68', 'ecommerce' => true],
                'status' => 'active',
            ]
        );
        $projectId = $project->id;

        $savePost = function (array $matchAttributes, array $updateValues) {
            $post = Post::withTrashed()->where($matchAttributes)->first();
            if ($post) {
                if ($post->trashed()) {
                    $post->restore();
                }
                $post->update(array_merge($matchAttributes, $updateValues));
                return $post;
            }
            return Post::create(array_merge($matchAttributes, $updateValues));
        };

        // 2. TAXONOMIES: PRODUCT CATEGORIES
        $productCategoriesData = [
            [
                'name' => 'Vật Tư Thiết Bị Điện',
                'slug' => 'vat-tu-thiet-bi-dien',
                'description' => 'Vật tư thiết bị lưới điện trung thế và hạ thế chính hãng đạt chuẩn EVN.',
                'order' => 1,
            ],
            [
                'name' => 'Đóng Cắt Trung Thế',
                'slug' => 'dong-cat-trung-the',
                'description' => 'Cầu chì tự rơi FCO, LBFCO, dao cách ly DS và phụ kiện bảo vệ mạch.',
                'order' => 2,
            ],
            [
                'name' => 'Máy Biến Áp & Trạm',
                'slug' => 'may-bien-ap-tram',
                'description' => 'Máy biến áp phân phối dầu và khô, trạm kiosk hợp bộ đạt tiêu chuẩn EVN.',
                'order' => 3,
            ],
            [
                'name' => 'Thiết Bị Đo Lường',
                'slug' => 'thiet-bi-do-luong',
                'description' => 'Biến dòng đo lường CT, biến điện áp VT, đồng hồ đo điện đa năng độ chính xác cao.',
                'order' => 4,
            ],
            [
                'name' => 'Cáp & Phụ Kiện Lưới Điện',
                'slug' => 'cap-phu-kien-luoi-dien',
                'description' => 'Cáp điện lực ngầm, dây dẫn trên không và phụ kiện đầu cáp co nguội co nhiệt.',
                'order' => 5,
            ],
            [
                'name' => 'Ống Trụ Bê Tông Ly Tâm',
                'slug' => 'ong-tru-be-tong-ly-tam',
                'description' => 'Cột điện và ống trụ bê tông cốt thép ly tâm dự ứng lực chất lượng cao.',
                'order' => 6,
            ],
            [
                'name' => 'Thiết Bị Năng Lượng Mặt Trời',
                'slug' => 'thiet-bi-nang-luong-mat-troi',
                'description' => 'Inverter hòa lưới, tấm pin quang điện và vật tư lắp đặt trọn gói hệ thống solar.',
                'order' => 7,
            ],
        ];

        $taxMap = [];
        foreach ($productCategoriesData as $catData) {
            $tax = Taxonomy::updateOrCreate(
                [
                    'slug' => $catData['slug'],
                    'taxonomy' => 'product_cat',
                ],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'order' => $catData['order'],
                    'project_id' => $projectId,
                    'status' => 'active',
                ]
            );
            $taxMap['cat_' . $catData['slug']] = $tax->id;
        }

        // TAXONOMIES: POST / NEWS CATEGORIES
        $newsCategoriesData = [
            [
                'name' => 'Tin Tức Ngành Điện',
                'slug' => 'tin-tuc-nganh-dien',
                'description' => 'Cập nhật thị trường điện năng và văn bản kỹ thuật mới nhất.',
                'order' => 1,
            ],
            [
                'name' => 'Dự Án Tiêu Biểu',
                'slug' => 'du-an-tieu-bieu',
                'description' => 'Các công trình trạm biến áp và lưới điện Đại Phúc 68 đã thực hiện.',
                'order' => 2,
            ],
            [
                'name' => 'Kiến Thức Kỹ Thuật',
                'slug' => 'kien-thuc-ky-thuat',
                'description' => 'Cẩm nang hướng dẫn lắp đặt, vận hành và bảo trì thiết bị điện.',
                'order' => 3,
            ],
            [
                'name' => 'Thông Báo Doanh Nghiệp',
                'slug' => 'thong-bao-doanh-nghiep',
                'description' => 'Hoạt động nội bộ, chính sách đại lý và chứng chỉ chất lượng.',
                'order' => 4,
            ],
        ];

        foreach ($newsCategoriesData as $catData) {
            $tax = Taxonomy::updateOrCreate(
                [
                    'slug' => $catData['slug'],
                    'taxonomy' => 'category',
                ],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'order' => $catData['order'],
                    'project_id' => $projectId,
                    'status' => 'active',
                ]
            );
            $taxMap['news_' . $catData['slug']] = $tax->id;
        }

        // TAXONOMIES: PROJECT CATEGORIES
        $projectCategoriesData = [
            [
                'name' => 'Nhà Hàng, Khách Sạn',
                'slug' => 'nha-hang-khach-san',
                'description' => 'Hệ thống cấp điện khu nghỉ dưỡng, khách sạn và nhà hàng cao cấp.',
                'order' => 1,
            ],
            [
                'name' => 'Công Trình Công Cộng',
                'slug' => 'cong-trinh-cong-cong',
                'description' => 'Trạm biến áp trung thế, trung tâm hội nghị và hạ tầng công cộng.',
                'order' => 2,
            ],
            [
                'name' => 'Nhà Ở & Biệt Thự',
                'slug' => 'nha-o-dan-dung',
                'description' => 'Hạ tầng điện khu đô thị sinh thái, biệt thự và nhà phố hiện đại.',
                'order' => 3,
            ],
        ];

        foreach ($projectCategoriesData as $catData) {
            $tax = Taxonomy::updateOrCreate(
                [
                    'slug' => $catData['slug'],
                    'taxonomy' => 'project_cat',
                ],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'order' => $catData['order'],
                    'project_id' => $projectId,
                    'status' => 'active',
                ]
            );
            $taxMap['proj_' . $catData['slug']] = $tax->id;
        }

        // TAXONOMIES: BRANDS
        $brandsData = [
            [
                'name' => 'Vina Electric',
                'slug' => 'vina-electric',
                'logo' => 'storage/media/partners/brand-vinaelectric.png',
                'order' => 1,
            ],
            [
                'name' => 'MBT',
                'slug' => 'mbt',
                'logo' => 'storage/media/brands/brand-mbt.svg',
                'order' => 2,
            ],
            [
                'name' => 'EMIC',
                'slug' => 'emic',
                'logo' => 'storage/media/partners/partner-thc.png',
                'order' => 3,
            ],
            [
                'name' => 'ABB',
                'slug' => 'abb',
                'logo' => 'storage/media/brands/brand-abb.svg',
                'order' => 4,
            ],
            [
                'name' => 'Schneider Electric',
                'slug' => 'schneider-electric',
                'logo' => 'storage/media/brands/brand-schneider.svg',
                'order' => 5,
            ],
            [
                'name' => 'Thibidi',
                'slug' => 'thibidi',
                'logo' => 'storage/media/brands/brand-thibidi.svg',
                'order' => 6,
            ],
            [
                'name' => '3M Electrical',
                'slug' => '3m-electrical',
                'logo' => 'storage/media/brands/brand-3m.svg',
                'order' => 7,
            ],
            [
                'name' => 'Shihlin Electric',
                'slug' => 'shihlin-electric',
                'logo' => 'storage/media/brands/brand-shihlin.svg',
                'order' => 8,
            ],
            [
                'name' => 'Cadivi',
                'slug' => 'cadivi',
                'logo' => 'storage/media/partners/partner-thanglong.png',
                'order' => 9,
            ],
        ];

        foreach ($brandsData as $bData) {
            $tax = Taxonomy::updateOrCreate(
                [
                    'slug' => $bData['slug'],
                    'taxonomy' => 'brand',
                ],
                [
                    'name' => $bData['name'],
                    'order' => $bData['order'],
                    'project_id' => $projectId,
                    'status' => 'active',
                    'meta_data' => ['logo' => $bData['logo']],
                ]
            );
            $taxMap['brand_' . $bData['slug']] = $tax->id;
        }

        // 3. PRODUCTS DATA SEEDING
        $productsData = [
            [
                'title' => 'Cầu Chì Tự Rơi FCO 24kV - 100A Polymer Vina Electric',
                'slug' => 'cau-chi-tu-roi-fco-24kv-100a-polymer',
                'excerpt' => 'Thiết bị bảo vệ quá tải và ngắn mạch đường dây trung thế 24kV đạt chuẩn EVN và tiêu chuẩn quốc tế IEC 60282-2.',
                'content' => '<p>Cầu chì tự rơi (Cut-Out FCO) Vina Electric là thiết bị phân đoạn và bảo vệ mạch điện trung thế hiệu quả hàng đầu cho các trạm biến áp phân phối ngoài trời. Sản phẩm được sản xuất bằng vật liệu cách điện Polymer (Silicone Rubber) cao cấp chống phóng điện bề mặt, kháng tia cực tím và chịu được sương muối ăn mòn.</p><p>Hệ thống ngàm tiếp điểm mạ bạc tinh khiết giúp tiếp xúc điện chắc chắn, giảm thiểu phát nhiệt khi mang tải định mức và đảm bảo dập hồ quang dứt khoát khi có sự cố ngắn mạch.</p>',
                'featured_image' => 'storage/media/products/product-cau-chi-tu-roi-vina-electric.png',
                'gallery' => [
                    'storage/media/products/product-cau-chi-tu-roi-vina-electric.png',
                    'storage/media/products/product1.png',
                    'storage/media/products/product2.png',
                    'storage/media/products/product3.png',
                ],
                'price' => 250000,
                'badge' => 'BÁN CHẠY NHẤT',
                'brand' => 'Vina Electric',
                'brand_slug' => 'vina-electric',
                'brand_logo' => 'storage/media/partners/brand-vinaelectric.png',
                'category' => 'Vật Tư Thiết Bị Điện',
                'category_slug' => 'vat-tu-thiet-bi-dien',
                'voltage' => '24kV',
                'standard' => 'IEC 60282-2, TCVN, EVN',
                'code' => 'FCO-24-100-VE',
                'specs' => [
                    ['name' => 'Điện áp định mức (Rated Voltage)', 'unit' => 'kV', 'value' => '24kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng điện định mức (Rated Current)', 'unit' => 'A', 'value' => '100A', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng cắt ngắn mạch (Breaking Capacity)', 'unit' => 'kA', 'value' => '12.5kA (sym) / 16kA (asym)', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Khoảng cách dòng rò (Creepage Distance)', 'unit' => 'mm', 'value' => '630mm', 'standard' => 'IEC 60815'],
                    ['name' => 'Điện áp xung chịu đựng sét (BIL)', 'unit' => 'kV', 'value' => '125kV / 150kV', 'standard' => 'IEC 60060'],
                    ['name' => 'Vật liệu cách điện chính', 'unit' => '-', 'value' => 'Polymer (Silicone Rubber)', 'standard' => 'TCVN'],
                    ['name' => 'Tiêu chuẩn kiểm định & nghiệm thu', 'unit' => '-', 'value' => 'EVN / Quatest 1', 'standard' => 'EVN'],
                ],
                'documents' => [
                    ['title' => 'Catalogue Kỹ Thuật FCO Vina Electric 24kV', 'meta' => 'PDF • 2.4 MB • Tiêu chuẩn EVN', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                    ['title' => 'Bản Vẽ Thiết Kế Kỹ Thuật & Kích Thước Lắp Đặt', 'meta' => 'PDF & DWG • 1.6 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                    ['title' => 'Chứng Chỉ Thử Nghiệm Xuất Xưởng (Type Test)', 'meta' => 'PDF • 850 KB • Quatest 1', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Cầu Chì Tự Rơi FCO 24kV - 200A Sứ ABB',
                'slug' => 'cau-chi-tu-roi-fco-24kv-200a-su',
                'excerpt' => 'Thiết bị FCO sứ cách điện chịu tải lớn 200A cho đường dây trung thế nhánh và trạm biến áp công nghiệp.',
                'content' => '<p>Cầu chì tự rơi FCO 24kV - 200A sứ cách điện thương hiệu ABB được thiết kế đặc thù cho các môi trường khí hậu khắc nghiệt. Thân sứ tráng men cao cấp có độ bền cơ học cao, chịu tải cơ học chống gãy vỡ khi thao tác đóng cắt bằng sào cách điện chuyên dụng.</p>',
                'featured_image' => 'storage/media/products/product1.png',
                'gallery' => [
                    'storage/media/products/product1.png',
                    'storage/media/products/product-cau-chi-tu-roi-vina-electric.png',
                    'storage/media/products/product2.png',
                ],
                'price' => 340000,
                'badge' => 'TIÊU CHUẨN EVN',
                'brand' => 'ABB',
                'brand_slug' => 'abb',
                'brand_logo' => 'storage/media/brands/brand-abb.svg',
                'category' => 'Đóng Cắt Trung Thế',
                'category_slug' => 'dong-cat-trung-the',
                'voltage' => '24kV',
                'standard' => 'IEC 60282-2, EVN',
                'code' => 'FCO-24-200-ABB',
                'specs' => [
                    ['name' => 'Điện áp định mức', 'unit' => 'kV', 'value' => '24kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng điện định mức', 'unit' => 'A', 'value' => '200A', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng cắt ngắn mạch', 'unit' => 'kA', 'value' => '16kA', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Vật liệu cách điện', 'unit' => '-', 'value' => 'Sứ cách điện tráng men', 'standard' => 'IEC 60383'],
                    ['name' => 'Tiêu chuẩn áp dụng', 'unit' => '-', 'value' => 'IEC 60282-2, EVN', 'standard' => 'EVN'],
                ],
                'documents' => [
                    ['title' => 'Catalogue Thiết Bị Đóng Cắt ABB Trung Thế', 'meta' => 'PDF • 3.1 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Cầu Chì Tự Rơi Cắt Có Tải LBFCO 24kV - 100A MBT',
                'slug' => 'cau-chi-tu-roi-cat-co-tai-lbfco-24kv-100a',
                'excerpt' => 'Thiết bị LBFCO có buồng dập hồ quang phụ khí, cho phép cắt dòng phụ tải lên đến 100A một cách an toàn.',
                'content' => '<p>Cầu chì tự rơi cắt có tải LBFCO MBT 24kV được tích hợp buồng dập hồ quang chuyên dụng và lưỡi dao phụ, cho phép kỹ thuật viên thao tác cắt trực tiếp dòng tải phụ của trạm biến áp ngoài trời mà không cần cắt điện nguồn tổng.</p>',
                'featured_image' => 'storage/media/products/product2.png',
                'gallery' => [
                    'storage/media/products/product2.png',
                    'storage/media/products/product1.png',
                    'storage/media/products/product3.png',
                ],
                'price' => 315000,
                'badge' => 'CÓ SẴN HÀNG',
                'brand' => 'MBT',
                'brand_slug' => 'mbt',
                'brand_logo' => 'storage/media/brands/brand-mbt.svg',
                'category' => 'Đóng Cắt Trung Thế',
                'category_slug' => 'dong-cat-trung-the',
                'voltage' => '24kV',
                'standard' => 'IEC 60282-2, ANSI C37.41',
                'code' => 'LBFCO-24-100-MBT',
                'specs' => [
                    ['name' => 'Điện áp định mức', 'unit' => 'kV', 'value' => '24kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng điện định mức mang tải', 'unit' => 'A', 'value' => '100A', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Khả năng cắt phụ tải', 'unit' => 'A', 'value' => '100A (cos phi 0.7)', 'standard' => 'ANSI C37.41'],
                    ['name' => 'Cơ cấu buồng dập hồ quang', 'unit' => '-', 'value' => 'Buồng dập phụ khí kín', 'standard' => 'IEC'],
                ],
                'documents' => [
                    ['title' => 'Hướng Dẫn Vận Hành LBFCO Cắt Có Tải MBT', 'meta' => 'PDF • 1.9 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Cầu Chì Tự Rơi Cắt Có Tải LBFCO 24kV - 200A Thibidi',
                'slug' => 'cau-chi-tu-roi-cat-co-tai-lbfco-24kv-200a',
                'excerpt' => 'Thiết bị LBFCO cắt tải công suất lớn 200A đạt chuẩn kiểm định ISO 9001:2015 từ tập đoàn Thibidi.',
                'content' => '<p>Dòng sản phẩm LBFCO Thibidi 24kV - 200A thích hợp cho các trạm biến áp dung lượng từ 560kVA đến 1250kVA. Khung đỡ thép mạ kẽm nhúng nóng dày dặn, chống gỉ sét bền bỉ trong môi trường sương muối.</p>',
                'featured_image' => 'storage/media/products/product3.png',
                'gallery' => [
                    'storage/media/products/product3.png',
                    'storage/media/products/product2.png',
                    'storage/media/products/product4.png',
                ],
                'price' => 395000,
                'badge' => 'ISO 9001:2015',
                'brand' => 'Thibidi',
                'brand_slug' => 'thibidi',
                'brand_logo' => 'storage/media/brands/brand-thibidi.svg',
                'category' => 'Đóng Cắt Trung Thế',
                'category_slug' => 'dong-cat-trung-the',
                'voltage' => '24kV',
                'standard' => 'IEC 60282-2, ISO 9001',
                'code' => 'LBFCO-24-200-THB',
                'specs' => [
                    ['name' => 'Điện áp định mức', 'unit' => 'kV', 'value' => '24kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng điện định mức', 'unit' => 'A', 'value' => '200A', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Cắt tải định mức', 'unit' => 'A', 'value' => '200A', 'standard' => 'IEC'],
                    ['name' => 'Tiêu chuẩn bảo vệ chất lượng', 'unit' => '-', 'value' => 'ISO 9001:2015 / EVN', 'standard' => 'ISO'],
                ],
                'documents' => [
                    ['title' => 'Catalogue & Bản Vẽ LBFCO Thibidi 200A', 'meta' => 'PDF • 2.2 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Cầu Chì Tự Rơi FCO 35kV - 100A Polymer Ngoài Trời Shihlin',
                'slug' => 'cau-chi-tu-roi-fco-35kv-100a-polymer-ngoai-troi',
                'excerpt' => 'Thiết bị FCO bảo vệ lưới điện 35kV ngoài trời với chiều dài đường rò lớn, kháng dòng rò vượt trội.',
                'content' => '<p>Dành riêng cho các vùng phân phối điện áp trung thế 35kV. Vật liệu Polymer cao cấp thiết kế nếp gấp lớn tối ưu hóa khoảng cách dòng rò, đảm bảo an toàn tuyệt đối khi thời tiết mưa dông, sương muối ẩm ướt.</p>',
                'featured_image' => 'storage/media/products/product4.png',
                'gallery' => [
                    'storage/media/products/product4.png',
                    'storage/media/products/product3.png',
                    'storage/media/products/product5.png',
                ],
                'price' => 490000,
                'badge' => 'BẢO HÀNH 24T',
                'brand' => 'Shihlin Electric',
                'brand_slug' => 'shihlin-electric',
                'brand_logo' => 'storage/media/brands/brand-shihlin.svg',
                'category' => 'Đóng Cắt Trung Thế',
                'category_slug' => 'dong-cat-trung-the',
                'voltage' => '35kV',
                'standard' => 'IEC 60282-2, ANSI C37.41',
                'code' => 'FCO-35-100-SH',
                'specs' => [
                    ['name' => 'Điện áp định mức', 'unit' => 'kV', 'value' => '35kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng điện định mức', 'unit' => 'A', 'value' => '100A', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Khoảng cách dòng rò', 'unit' => 'mm', 'value' => '850mm', 'standard' => 'IEC 60815'],
                    ['name' => 'Điện áp xung sét (BIL)', 'unit' => 'kV', 'value' => '170kV / 200kV', 'standard' => 'IEC 60060'],
                ],
                'documents' => [
                    ['title' => 'Hồ Sơ Kỹ Thuật Shihlin 35kV FCO', 'meta' => 'PDF • 2.6 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Cầu Chì Tự Rơi FCO 35kV - 200A Schneider Electric',
                'slug' => 'cau-chi-tu-roi-fco-35kv-200a-tieu-chuan-iec',
                'excerpt' => 'Giải pháp bảo vệ trung thế cao cấp của Schneider Electric với độ tin cậy và thẩm mỹ công trình cao.',
                'content' => '<p>Cầu chì FCO 35kV Schneider Electric được sản xuất trên dây chuyền tự động hóa đạt tiêu chuẩn Châu Âu, tiếp điểm đồng dẻo mạ bạc kép chịu lực nén cơ học đàn hồi cao, đóng ngắt êm ái và chuẩn xác.</p>',
                'featured_image' => 'storage/media/products/product5.png',
                'gallery' => [
                    'storage/media/products/product5.png',
                    'storage/media/products/product4.png',
                    'storage/media/products/product1.png',
                ],
                'price' => 520000,
                'badge' => 'TIÊU CHUẨN IEC',
                'brand' => 'Schneider Electric',
                'brand_slug' => 'schneider-electric',
                'brand_logo' => 'storage/media/brands/brand-schneider.svg',
                'category' => 'Đóng Cắt Trung Thế',
                'category_slug' => 'dong-cat-trung-the',
                'voltage' => '35kV',
                'standard' => 'IEC 60282-2, CE',
                'code' => 'FCO-35-200-SCH',
                'specs' => [
                    ['name' => 'Điện áp định mức', 'unit' => 'kV', 'value' => '35kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng điện định mức', 'unit' => 'A', 'value' => '200A', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Khả năng chịu dòng ngắn mạch', 'unit' => 'kA', 'value' => '16kA / 1s', 'standard' => 'IEC'],
                ],
                'documents' => [
                    ['title' => 'Schneider Electric Medium Voltage Switchgear Catalogue', 'meta' => 'PDF • 4.5 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Cầu Chì Tự Rơi Cắt Có Tải LBFCO 35kV - 100A 3M Electrical',
                'slug' => 'cau-chi-tu-roi-cat-co-tai-lbfco-35kv-100a',
                'excerpt' => 'Thiết bị LBFCO 35kV công nghệ vật liệu cách điện Polymer đặc quyền từ tập đoàn 3M.',
                'content' => '<p>Sản phẩm sử dụng hợp chất silicone kháng nước tối ưu của 3M, giúp giọt nước co cụm không tạo vệt liên tục trên bề mặt cách điện, giảm tối đa xác suất đánh lửa qua bề mặt trong mùa sương ẩm.</p>',
                'featured_image' => 'storage/media/products/product-extracted-1.png',
                'gallery' => [
                    'storage/media/products/product-extracted-1.png',
                    'storage/media/products/product5.png',
                    'storage/media/products/product2.png',
                ],
                'price' => 560000,
                'badge' => 'CHẤT LƯỢNG CAO',
                'brand' => '3M Electrical',
                'brand_slug' => '3m-electrical',
                'brand_logo' => 'storage/media/brands/brand-3m.svg',
                'category' => 'Đóng Cắt Trung Thế',
                'category_slug' => 'dong-cat-trung-the',
                'voltage' => '35kV',
                'standard' => 'IEC 60282-2, 3M Spec',
                'code' => 'LBFCO-35-100-3M',
                'specs' => [
                    ['name' => 'Điện áp định mức', 'unit' => 'kV', 'value' => '35kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng điện định mức', 'unit' => 'A', 'value' => '100A', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Khoảng cách dòng rò', 'unit' => 'mm', 'value' => '920mm', 'standard' => 'IEC 60815'],
                ],
                'documents' => [
                    ['title' => '3M Electrical Technical Datasheet', 'meta' => 'PDF • 1.7 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Ống Chì Thay Thế Cho FCO 24kV 100A Dập Hồ Quang',
                'slug' => 'ong-chi-thay-the-cho-fco-24kv-100a-dap-ho-quang',
                'excerpt' => 'Ống chì phụ tùng thay thế chuẩn cho các loại cầu chì tự rơi FCO 24kV 100A, sợi thủy tinh tẩm epoxy.',
                'content' => '<p>Ống chì (Fuse Tube) dự phòng dùng thay thế cho FCO 24kV khi dây chì nổ sự cố. Lớp ngoài làm từ sợi thủy tinh tẩm keo epoxy chịu lực, lớp lót trong làm bằng vật liệu tổng hợp tự sinh khí dập hồ quang tức thì.</p>',
                'featured_image' => 'storage/media/products/product-extracted-2.png',
                'gallery' => [
                    'storage/media/products/product-extracted-2.png',
                    'storage/media/products/product-extracted-1.png',
                    'storage/media/products/product1.png',
                ],
                'price' => 115000,
                'badge' => 'BÁN CHẠY NHẤT',
                'brand' => 'ABB',
                'brand_slug' => 'abb',
                'brand_logo' => 'storage/media/brands/brand-abb.svg',
                'category' => 'Vật Tư Thiết Bị Điện',
                'category_slug' => 'vat-tu-thiet-bi-dien',
                'voltage' => '24kV',
                'standard' => 'IEC 60282-2',
                'code' => 'FT-24-100',
                'specs' => [
                    ['name' => 'Điện áp định mức', 'unit' => 'kV', 'value' => '24kV', 'standard' => 'IEC 60282-2'],
                    ['name' => 'Dòng định mức ống', 'unit' => 'A', 'value' => '100A', 'standard' => 'IEC'],
                    ['name' => 'Vật liệu chế tạo', 'unit' => '-', 'value' => 'Sợi thủy tinh Filament + Lớp sinh khí', 'standard' => 'TCVN'],
                ],
                'documents' => [
                    ['title' => 'Thông Số Ống Chì Thay Thế FCO', 'meta' => 'PDF • 800 KB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Dây Chì Trung Thế 24kV 5A - 100A Vina Electric',
                'slug' => 'day-chi-trung-the-24kv-5a-100a',
                'excerpt' => 'Dây chì chảy loại K và T chất lượng cao, đứt chính xác theo đường cong đặc tính thời gian - dòng điện.',
                'content' => '<p>Dây chì chảy bảo vệ trung thế Vina Electric đạt chuẩn TCVN và EVN, chế tạo bằng hợp kim bạc - đồng tinh chuẩn, dập chì chắc chắn, thời gian tác động chính xác bảo vệ máy biến áp và chống quá tải đường dây.</p>',
                'featured_image' => 'storage/media/products/product-cau-chi-tu-roi-vina-electric.png',
                'gallery' => [
                    'storage/media/products/product-cau-chi-tu-roi-vina-electric.png',
                    'storage/media/products/product3.png',
                ],
                'price' => 35000,
                'badge' => 'TIÊU CHUẨN EVN',
                'brand' => 'Vina Electric',
                'brand_slug' => 'vina-electric',
                'brand_logo' => 'storage/media/partners/brand-vinaelectric.png',
                'category' => 'Vật Tư Thiết Bị Điện',
                'category_slug' => 'vat-tu-thiet-bi-dien',
                'voltage' => '24kV',
                'standard' => 'TCVN, EVN',
                'code' => 'FL-24-MIX',
                'specs' => [
                    ['name' => 'Cấp điện áp', 'unit' => 'kV', 'value' => '24kV', 'standard' => 'TCVN'],
                    ['name' => 'Dải dòng điện định mức', 'unit' => 'A', 'value' => '5A, 10A, 15A, 20A, 30A, 50A, 100A', 'standard' => 'IEC'],
                    ['name' => 'Loại đặc tính chảy', 'unit' => '-', 'value' => 'Loại K (Nhanh) / Loại T (Chậm)', 'standard' => 'ANSI'],
                ],
                'documents' => [
                    ['title' => 'Đặc Tính Chảy Dây Chì Trung Thế', 'meta' => 'PDF • 1.2 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Máy Biến Áp Dầu MBT 3 Pha 1000kVA 22/0.4kV',
                'slug' => 'may-bien-ap-dau-mbt-3-pha-1000kva',
                'excerpt' => 'Máy biến áp ngâm dầu truyền tải điện năng hiệu suất cao, tổn thất thấp theo Quyết định 62/EVN.',
                'content' => '<p>Máy biến áp phân phối 3 pha MBT ngâm dầu công suất 1000kVA được chế tạo từ tôn silic định hướng từ tính cao, cuộn dây đồng lá chịu lực ngắn mạch tốt. Sản phẩm tiết kiệm năng lượng, độ ồn thấp, kiểm định nghiêm ngặt bởi Trung tâm Đo lường EVN trước khi xuất xưởng.</p>',
                'featured_image' => 'storage/media/products/product2.png',
                'gallery' => [
                    'storage/media/products/product2.png',
                    'storage/media/products/product3.png',
                    'storage/media/products/product4.png',
                ],
                'price' => 0,
                'badge' => 'DỰ ÁN TRỌNG ĐIỂM',
                'brand' => 'MBT',
                'brand_slug' => 'mbt',
                'brand_logo' => 'storage/media/brands/brand-mbt.svg',
                'category' => 'Máy Biến Áp & Trạm',
                'category_slug' => 'may-bien-ap-tram',
                'voltage' => '22/0.4kV',
                'standard' => 'QĐ 62/EVN, IEC 60076',
                'code' => 'MBA-MBT-1000',
                'specs' => [
                    ['name' => 'Công suất định mức', 'unit' => 'kVA', 'value' => '1000kVA', 'standard' => 'IEC 60076'],
                    ['name' => 'Điện áp sơ cấp / thứ cấp', 'unit' => 'kV', 'value' => '22 / 0.4kV', 'standard' => 'EVN'],
                    ['name' => 'Tổ đấu dây', 'unit' => '-', 'value' => 'Dyn-11', 'standard' => 'IEC'],
                    ['name' => 'Tần số làm việc', 'unit' => 'Hz', 'value' => '50Hz', 'standard' => 'TCVN'],
                    ['name' => 'Vật liệu cuộn dây', 'unit' => '-', 'value' => 'Đồng tinh khiết 99.99%', 'standard' => 'EVN'],
                ],
                'documents' => [
                    ['title' => 'Catalogue Máy Biến Áp Phân Phối MBT', 'meta' => 'PDF • 5.0 MB • Chuẩn EVN', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Biến Dòng Đo Lường EMIC Trung Thế 24kV Cấp 0.5',
                'slug' => 'bien-dong-do-luong-emic-trung-the-24kv',
                'excerpt' => 'Máy biến dòng đo lường trung thế ngâm dầu và đúc epoxy độ chính xác cao cấp 0.5 từ EMIC.',
                'content' => '<p>Máy biến dòng EMIC phục vụ đo đếm điện năng thương mại và cấp tín hiệu bảo vệ rơle cho các tủ trạm kiosk và trạm treo ngoài trời. Cấp chính xác 0.5/5P20 đảm bảo hiển thị đúng trị số dòng điện thực tế trên lưới.</p>',
                'featured_image' => 'storage/media/products/product4.png',
                'gallery' => [
                    'storage/media/products/product4.png',
                    'storage/media/products/product1.png',
                    'storage/media/products/product5.png',
                ],
                'price' => 380000,
                'badge' => 'ĐỘ CHÍNH XÁC CAO',
                'brand' => 'EMIC',
                'brand_slug' => 'emic',
                'brand_logo' => 'storage/media/partners/partner-thc.png',
                'category' => 'Thiết Bị Đo Lường',
                'category_slug' => 'thiet-bi-do-luong',
                'voltage' => '24kV',
                'standard' => 'IEC 61869-2, TCVN 7697-2',
                'code' => 'CT-EMIC-24',
                'specs' => [
                    ['name' => 'Cấp điện áp cách điện', 'unit' => 'kV', 'value' => '24kV', 'standard' => 'IEC 61869-2'],
                    ['name' => 'Tỷ số biến dòng định mức', 'unit' => 'A', 'value' => '50/5A - 600/5A', 'standard' => 'TCVN'],
                    ['name' => 'Cấp chính xác', 'unit' => '-', 'value' => '0.5 / 5P20', 'standard' => 'IEC'],
                    ['name' => 'Dung lượng định mức cuộn thứ cấp', 'unit' => 'VA', 'value' => '15VA - 30VA', 'standard' => 'IEC'],
                ],
                'documents' => [
                    ['title' => 'Catalogue & Sơ Đồ Đấu Dây CT EMIC 24kV', 'meta' => 'PDF • 2.1 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
            [
                'title' => 'Ống Trụ Bê Tông Cốt Thép Ly Tâm Dự Ứng Lực',
                'slug' => 'ong-tru-be-tong-cot-thep-ly-tam',
                'excerpt' => 'Cột điện và ống trụ bê tông ly tâm dự ứng lực mác cao phục vụ thi công lưới điện trung hạ thế.',
                'content' => '<p>Ống trụ bê tông cốt thép ly tâm dự ứng lực Đại Phúc 68 sản xuất trên dây chuyền quay ly tâm hiện đại, mác bê tông từ B40 trở lên. Bề mặt cột phẳng mịn, không rỗ tổ ong, khả năng chịu lực uốn gãy và lực kéo đầu cột đạt tiêu chuẩn TCVN 5847:2016.</p>',
                'featured_image' => 'storage/media/projects/project-detail-hero.jpg',
                'gallery' => [
                    'storage/media/projects/project-detail-hero.jpg',
                    'storage/media/projects/project-item-1.png',
                ],
                'price' => 0,
                'badge' => 'CÔNG TRÌNH EVN',
                'brand' => 'Đại Phúc 68',
                'brand_slug' => 'vina-electric',
                'brand_logo' => 'themes/daiphuc68/assets/icons/logo-daiphuc68.svg',
                'category' => 'Ống Trụ Bê Tông Ly Tâm',
                'category_slug' => 'ong-tru-be-tong-ly-tam',
                'voltage' => 'Trung - Hạ thế',
                'standard' => 'TCVN 5847:2016',
                'code' => 'BTLT-DP-14M',
                'specs' => [
                    ['name' => 'Chiều dài thân cột', 'unit' => 'm', 'value' => '10m - 14m - 20m', 'standard' => 'TCVN 5847'],
                    ['name' => 'Cốt thép chịu lực', 'unit' => '-', 'value' => 'Thép cường độ cao dự ứng lực PC Wire', 'standard' => 'JIS / ASTM'],
                    ['name' => 'Mác bê tông chịu nén', 'unit' => 'MPa', 'value' => '>= 40 MPa', 'standard' => 'TCVN'],
                ],
                'documents' => [
                    ['title' => 'Bảng Quy Cách & Tải Trọng Đầu Cột Bê Tông Ly Tâm', 'meta' => 'PDF • 1.5 MB', 'file' => 'themes/daiphuc68/assets/ho-so-nang-luc.pdf'],
                ],
            ],
        ];

        foreach ($productsData as $pData) {
            $post = $savePost(
                [
                    'slug' => $pData['slug'],
                    'post_type' => 'product',
                ],
                [
                    'title' => $pData['title'],
                    'excerpt' => $pData['excerpt'],
                    'content' => $pData['content'],
                    'featured_image' => $pData['featured_image'],
                    'status' => 'published',
                    'published_at' => now(),
                    'author_id' => $authorId,
                    'project_id' => $projectId,
                    'meta_data' => [
                        'code' => $pData['code'],
                        'price' => $pData['price'],
                        'badge' => $pData['badge'],
                        'brand' => $pData['brand'],
                        'brand_logo' => $pData['brand_logo'],
                        'category' => $pData['category'],
                        'voltage' => $pData['voltage'],
                        'standard' => $pData['standard'],
                        'specs' => $pData['specs'],
                        'documents' => $pData['documents'],
                        'gallery' => $pData['gallery'],
                    ],
                ]
            );

            // Connect taxonomies
            $catTaxId = $taxMap['cat_' . $pData['category_slug']] ?? null;
            $brandTaxId = $taxMap['brand_' . $pData['brand_slug']] ?? null;

            if ($catTaxId) {
                DB::table('term_relationships')->updateOrInsert(
                    ['object_id' => $post->id, 'term_taxonomy_id' => $catTaxId],
                    ['order' => 0]
                );
            }
            if ($brandTaxId) {
                DB::table('term_relationships')->updateOrInsert(
                    ['object_id' => $post->id, 'term_taxonomy_id' => $brandTaxId],
                    ['order' => 1]
                );
            }
        }

        // 4. PROJECTS DATA SEEDING
        $projectsData = [
            // CATEGORY 1: Nhà Hàng, Khách Sạn (nha-hang-khach-san)
            [
                'title' => 'Nhà Hàng The Long',
                'slug' => 'nha-hang-the-long',
                'excerpt' => 'Công trình ẩm thực cao cấp ven sông với hệ thống cấp điện trạm biến áp Kiosk 630kVA chuẩn vận hành liên tục 24/7.',
                'content' => '<p>Trong dự án Nhà Hàng The Long, yêu cầu kỹ thuật khắt khe về cấp điện liên tục không gián đoạn, bảo đảm an toàn phòng cháy chữa cháy và mỹ quan cảnh quan bờ sông. Đại Phúc 68 cung cấp đồng bộ trạm kiosk hợp bộ 630kVA 22/0.4kV, tủ RMU Schneider và cáp ngầm chống thấm Cadivi, bàn giao nghiệm thu vượt tiến độ 15 ngày.</p>',
                'featured_image' => 'storage/media/projects/project-nha-hang-the-long.jpg',
                'category' => 'Nhà Hàng, Khách Sạn',
                'category_slug' => 'nha-hang-khach-san',
                'client' => 'Tập đoàn The Long Hospitality',
                'location' => 'Bến Nghé, Quận 1, TP. Hồ Chí Minh',
                'scale' => '3.500 m² - Trạm TBA 630kVA',
                'year' => '2024',
                'scope' => 'Thiết kế thi công trạm Kiosk, cấp cáp ngầm trung thế 22kV và tủ phân phối tổng MSB.',
                'challenges' => 'Mặt bằng sát mép sông yêu cầu chống ẩm ngập nước và hạn chế tiếng ồn máy biến áp.',
                'gallery' => [
                    'storage/media/projects/project-nha-hang-the-long.jpg',
                    'storage/media/projects/project-detail-1.jpg',
                    'storage/media/projects/project-detail-2.jpg',
                    'storage/media/projects/project-gallery-1.jpg',
                ],
            ],
            [
                'title' => 'Grand Palace Hotel & Resort',
                'slug' => 'grand-palace-hotel',
                'excerpt' => 'Tổ hợp nghỉ dưỡng 5 sao quốc tế với 2 trạm biến áp phân phối 1000kVA và hệ thống máy phát dự phòng đồng bộ.',
                'content' => '<p>Hệ thống điện phục vụ khu nghỉ dưỡng biển đòi hỏi khả năng chống chịu sương muối và nồng độ ẩm cực cao. Toàn bộ thiết bị cầu chì tự rơi FCO và chống sét van Đại Phúc 68 lắp đặt sử dụng dòng Polymer chuyên dụng biển đảo.</p>',
                'featured_image' => 'storage/media/projects/project-item-3.jpg',
                'category' => 'Nhà Hàng, Khách Sạn',
                'category_slug' => 'nha-hang-khach-san',
                'client' => 'Grand Palace Investment Corp',
                'location' => 'Bãi Dài, Phú Quốc, Kiên Giang',
                'scale' => '250 phòng 5 sao - 2 TBA 1000kVA',
                'year' => '2023',
                'scope' => 'Thi công trạm biến áp 2x1000kVA, tủ ATS chuyển nguồn tự động và hệ thống phân phối nội khu.',
                'challenges' => 'Khí hậu sương muối biển ăn mòn cao, yêu cầu thiết bị mạ nhúng nóng và polymer cao cấp.',
                'gallery' => [
                    'storage/media/projects/project-item-3.jpg',
                    'storage/media/projects/project-gallery-1.jpg',
                    'storage/media/projects/project-gallery-3.jpg',
                ],
            ],
            [
                'title' => 'Marina Bay Beach Resort & Spa',
                'slug' => 'resort-marina-bay',
                'excerpt' => 'Quần thể khách sạn nghỉ dưỡng ven biển cao cấp với hệ thống cấp điện thông minh và trạm biến áp ngầm chống ngập.',
                'content' => '<p>Marina Bay Beach Resort & Spa đòi hỏi sự hòa quyện tuyệt đối giữa hạ tầng điện ngầm hiện đại và cảnh quan sinh thái nguyên sơ ven bờ biển Đà Nẵng. Đại Phúc 68 đã triển khai mạng lưới cáp ngầm trung thế, tủ đóng cắt RMU ngoài trời chuẩn IP67 và hệ thống biến áp dầu chống ăn mòn sương muối vượt trội.</p>',
                'featured_image' => 'storage/media/projects/project-detail-1.jpg',
                'category' => 'Nhà Hàng, Khách Sạn',
                'category_slug' => 'nha-hang-khach-san',
                'client' => 'Tập Đoàn Du Lịch Marina',
                'location' => 'Bán đảo Sơn Trà, TP. Đà Nẵng',
                'scale' => '180 Villa & Bungalow cao cấp - 3 TBA 800kVA',
                'year' => '2023',
                'scope' => 'Cung ứng vật tư thiết bị trạm biến áp 3x800kVA, cáp ngầm trung thế hạ thế và tủ phân phối ngoài trời IP65.',
                'challenges' => 'Độ dốc địa hình đồi núi ven biển đòi hỏi giải pháp định vị và neo kéo cáp ngầm phức tạp.',
                'gallery' => [
                    'storage/media/projects/project-detail-1.jpg',
                    'storage/media/projects/project-detail-hero.jpg',
                    'storage/media/projects/project-gallery-2.jpg',
                ],
            ],
            [
                'title' => 'Khách Sạn Crown Plaza Luxury',
                'slug' => 'khach-san-crown-plaza',
                'excerpt' => 'Khách sạn thương mại chuẩn 5 sao 28 tầng với hệ thống máy biến áp khô chống cháy nổ và tủ phân phối tự động BMS.',
                'content' => '<p>Crown Plaza Luxury sở hữu 28 tầng cao ốc ngay tại lõi trung tâm thủ đô. Để bảo đảm an toàn PCCC tuyệt đối trong lòng khối đế công trình, Đại Phúc 68 đã tư vấn và cung cấp máy biến áp khô đúc chân không thế hệ mới cùng hệ thống thanh cái dẫn điện Busway chịu dòng tải đỉnh 2500A.</p>',
                'featured_image' => 'storage/media/projects/project-item-2.jpg',
                'category' => 'Nhà Hàng, Khách Sạn',
                'category_slug' => 'nha-hang-khach-san',
                'client' => 'Crown Hospitality JSC',
                'location' => 'Hoàn Kiếm, TP. Hà Nội',
                'scale' => '28 tầng nổi - 3 tầng hầm - 3 TBA khô 1250kVA',
                'year' => '2024',
                'scope' => 'Cung cấp trọn bộ tủ RMU trung thế, MBA khô đúc chân không và hệ thống thanh dẫn Busway 2500A.',
                'challenges' => 'Không gian đặt trạm tại tầng hầm trung tâm đô thị đòi hỏi an toàn PCCC tuyệt đối và thông gió cưỡng bức.',
                'gallery' => [
                    'storage/media/projects/project-item-2.jpg',
                    'storage/media/projects/project-gallery-3.jpg',
                    'storage/media/projects/project-detail-2.jpg',
                ],
            ],

            // CATEGORY 2: Công Trình Công Cộng (cong-trinh-cong-cong)
            [
                'title' => 'Trung Tâm Triển Lãm & Hội Nghị Quốc Tế',
                'slug' => 'trung-tam-trien-lam-hoi-nghi',
                'excerpt' => 'Công trình biểu tượng văn hóa đô thị với tổng diện tích sàn 45.000m² và mạng lưới điện ngầm thông minh.',
                'content' => '<p>Hệ thống cơ điện quy mô lớn đáp ứng đồng thời các sự kiện triển lãm quốc tế với phụ tải biến động liên tục. Đại Phúc 68 cung cấp tủ trung thế RMU Schneider, máy biến áp khô chống cháy và hệ thống giám sát SCADA hiện đại.</p>',
                'featured_image' => 'storage/media/projects/project-item-4.jpg',
                'category' => 'Công Trình Công Cộng',
                'category_slug' => 'cong-trinh-cong-cong',
                'client' => 'Ban Quản Lý Dự Án Trọng Điểm TP',
                'location' => 'Khu đô thị mới Thủ Thiêm, TP. HCM',
                'scale' => '45.000 m² sàn - Tủ RMU 24kV',
                'year' => '2024',
                'scope' => 'Cung cấp thiết bị đóng cắt trung thế, cáp chống cháy hạ thế và trạm biến áp khô.',
                'challenges' => 'Tiêu chuẩn an toàn PCCC khắt khe của công trình tập trung đông người.',
                'gallery' => [
                    'storage/media/projects/project-item-4.jpg',
                    'storage/media/projects/project-detail-hero.jpg',
                    'storage/media/projects/project-detail-5.jpg',
                ],
            ],
            [
                'title' => 'Trạm Biến Áp Trung Thế 110kV KCN Yên Phong',
                'slug' => 'tram-bien-ap-110kv-yen-phong',
                'excerpt' => 'Trọng điểm cấp điện khu công nghiệp công nghệ cao với công suất 2x63MVA đạt tiêu chuẩn EVN.',
                'content' => '<p>Dự án cung cấp vật tư phụ kiện tiếp địa, dao cách ly 110kV và cột bê tông ly tâm dự ứng lực cho tuyến đường dây đấu nối trạm 110kV Yên Phong, bảo đảm cấp điện ổn định cho các nhà máy công nghệ cao.</p>',
                'featured_image' => 'storage/media/projects/project-item-5.jpg',
                'category' => 'Công Trình Công Cộng',
                'category_slug' => 'cong-trinh-cong-cong',
                'client' => 'Tổng Công Ty Điện Lực Miền Bắc (EVNNPC)',
                'location' => 'KCN Yên Phong, Bắc Ninh',
                'scale' => '2 x 63MVA - Cấp điện áp 110/22kV',
                'year' => '2024',
                'scope' => 'Cung ứng thiết bị nhất thứ, trụ đỡ thiết bị, cáp quang OPGW và phụ kiện tiếp địa.',
                'challenges' => 'Yêu cầu nghiệm thu kỹ thuật gắt gao của EVN trước khi đóng điện mang tải.',
                'gallery' => [
                    'storage/media/projects/project-item-5.jpg',
                    'storage/media/projects/project-item-1.png',
                    'storage/media/projects/project-detail-1.jpg',
                ],
            ],
            [
                'title' => 'Bệnh Viện Đa Khoa Quốc Tế Tân Triều',
                'slug' => 'benh-vien-da-khoa-quoc-te',
                'excerpt' => 'Tổ hợp y tế chất lượng cao quy mô 800 giường với hệ thống điện y tế cách ly nguồn UPS kép IT chuẩn y khoa.',
                'content' => '<p>Bệnh viện Đa Khoa Quốc Tế là công trình trọng điểm cứu chữa bệnh nhân đòi hỏi chất lượng nguồn điện đặc biệt khắt khe. Đại Phúc 68 cung ứng các dòng tủ điện hạ thế chuyên dụng, hệ thống chuyển nguồn ATS siêu tốc dưới 5ms và giải pháp lọc sóng hài chủ động bảo vệ thiết bị y tế chẩn đoán hình ảnh cao cấp.</p>',
                'featured_image' => 'storage/media/projects/project-detail-hero.jpg',
                'category' => 'Công Trình Công Cộng',
                'category_slug' => 'cong-trinh-cong-cong',
                'client' => 'Ban Quản Lý Dự Án Sở Y Tế',
                'location' => 'Hà Đông, TP. Hà Nội',
                'scale' => '800 giường bệnh - 4 TBA 1600kVA',
                'year' => '2023',
                'scope' => 'Thi công trạm biến áp trung thế, hệ thống điện cấp nguồn phòng mổ cách ly an toàn và tủ ATS kép.',
                'challenges' => 'Nguồn điện phục vụ phòng mổ và hồi sức tích cực không được gián đoạn dù chỉ 1 mili-giây.',
                'gallery' => [
                    'storage/media/projects/project-detail-hero.jpg',
                    'storage/media/projects/project-detail-5.jpg',
                    'storage/media/projects/project-gallery-1.jpg',
                ],
            ],
            [
                'title' => 'Gói Thầu Cấp Điện Phân Khu Sân Bay Long Thành',
                'slug' => 'cang-hang-khong-quoc-te-long-thanh',
                'excerpt' => 'Hạ tầng cung ứng cáp ngầm trung thế 22kV và trạm phân phối cho các khu chức năng hậu cần hàng không.',
                'content' => '<p>Dự án cảng hàng không quốc tế Long Thành là siêu dự án quốc gia. Đại Phúc 68 tự hào là nhà cung cấp vật tư thiết bị đầu cáp trung thế 3M, cầu chì tự rơi Polymer Vina Electric và hệ thống tiếp địa chống sét đạt tiêu chuẩn quốc tế ICAO.</p>',
                'featured_image' => 'storage/media/projects/project-item-1.png',
                'category' => 'Công Trình Công Cộng',
                'category_slug' => 'cong-trinh-cong-cong',
                'client' => 'Tổng Công Ty Cảng Hàng Không Việt Nam (ACV)',
                'location' => 'Huyện Long Thành, Đồng Nai',
                'scale' => 'Phân khu kỹ thuật 120 ha - Tuyến cáp 22kV dài 18km',
                'year' => '2024',
                'scope' => 'Cung ứng phụ kiện đấu nối cáp ngầm 3M, cầu dao phụ tải LBS và tủ RMU chuyên dụng sân bay.',
                'challenges' => 'Khối lượng thi công trải dài trên công trường đại quy mô, yêu cầu kiểm định chất lượng gắt gao.',
                'gallery' => [
                    'storage/media/projects/project-item-1.png',
                    'storage/media/projects/project-item-5.jpg',
                    'storage/media/projects/project-detail-2.jpg',
                ],
            ],

            // CATEGORY 3: Nhà Ở & Biệt Thự (nha-o-dan-dung)
            [
                'title' => 'Villa Park Residence',
                'slug' => 'villa-park-residence',
                'excerpt' => 'Khu đô thị biệt thự sinh thái cao cấp với mạng lưới cáp ngầm trung thế 22kV đạt chuẩn ngầm hóa đô thị hiện đại.',
                'content' => '<p>Đại Phúc 68 là đơn vị cung ứng vật tư FCO Vina Electric, máy biến áp dầu MBT và phụ kiện đường dây ngầm hóa cho toàn bộ quần thể Villa Park Residence. Hệ thống vận hành ổn định qua 3 mùa mưa dông với tỷ lệ sự cố bằng 0.</p>',
                'featured_image' => 'storage/media/projects/project-villa-park.jpg',
                'category' => 'Nhà Ở & Biệt Thự',
                'category_slug' => 'nha-o-dan-dung',
                'client' => 'Khang Điền Group',
                'location' => 'TP. Thủ Đức, TP. Hồ Chí Minh',
                'scale' => '11.2 ha - Hạ ngầm 100% cáp 22kV',
                'year' => '2023',
                'scope' => 'Cung ứng trọn gói vật tư trung thế, tủ phân phối hạ thế và hệ thống tiếp địa an toàn.',
                'challenges' => 'Mật độ thi công hạ tầng giao cắt yêu cầu kiểm soát an toàn điện nghiệm ngặt.',
                'gallery' => [
                    'storage/media/projects/project-villa-park.jpg',
                    'storage/media/projects/project-detail-3.jpg',
                    'storage/media/projects/project-detail-4.jpg',
                    'storage/media/projects/project-gallery-2.jpg',
                ],
            ],
            [
                'title' => 'Biệt Thự Vườn Thảo Điền Eco Sky',
                'slug' => 'biet-thu-vuon-thao-dien',
                'excerpt' => 'Biệt thự kiến trúc nhiệt đới bền vững tích hợp điện mặt trời mái nhà 25kWp và trạm sạc xe điện nhanh.',
                'content' => '<p>Công trình kết hợp hoàn hảo giữa kiến trúc xanh và giải pháp năng lượng tái tạo. Hệ thống Inverter hòa lưới thông minh kèm pin lưu trữ Lithium giúp gia chủ tự chủ 80% năng lượng tiêu thụ hàng ngày.</p>',
                'featured_image' => 'storage/media/projects/project-item-6.jpg',
                'category' => 'Nhà Ở & Biệt Thự',
                'category_slug' => 'nha-o-dan-dung',
                'client' => 'Chủ đầu tư tư nhân',
                'location' => 'Thảo Điền, TP. Thủ Đức, TP. HCM',
                'scale' => '1.200 m² - Solar Roof 25kWp & EV Charger',
                'year' => '2024',
                'scope' => 'Thiết kế thi công hệ thống điện năng lượng mặt trời áp mái và tủ điện thông minh Smart Home.',
                'challenges' => 'Đảm bảo thẩm mỹ mái nhà tinh tế và tối ưu góc nghiêng bức xạ mặt trời.',
                'gallery' => [
                    'storage/media/projects/project-item-6.jpg',
                    'storage/media/projects/project-detail-2.jpg',
                    'storage/media/projects/project-detail-4.jpg',
                ],
            ],
            [
                'title' => 'Khu Biệt Thự Đảo Ecopark Grand The Island',
                'slug' => 'khu-do-thi-ecopark-villas',
                'excerpt' => 'Quần thể biệt thự đảo siêu sang với hạ tầng điện ngầm hóa toàn diện, trạm Kiosk tích hợp cảnh quan cây xanh.',
                'content' => '<p>Ecopark Grand The Island là biểu tượng đỉnh cao của phong cách sống thượng lưu giữa thiên nhiên. Đại Phúc 68 cung cấp các giải pháp trạm Kiosk hợp bộ giảm ồn tối đa, tủ phân phối chống ngập IP68 và tuyến cáp ngầm trung thế được thi công chuẩn mực cao nhất.</p>',
                'featured_image' => 'storage/media/projects/project-detail-3.jpg',
                'category' => 'Nhà Ở & Biệt Thự',
                'category_slug' => 'nha-o-dan-dung',
                'client' => 'Tập đoàn Ecopark',
                'location' => 'Văn Giang, Hưng Yên',
                'scale' => '60 ha - 280 căn biệt thự đơn lập mặt nước',
                'year' => '2023',
                'scope' => 'Cung cấp trạm biến áp Kiosk hợp bộ công nghệ thấp âm, tủ RMU chống nước và cáp ngầm trung thế.',
                'challenges' => 'Toàn bộ biệt thự nằm trên ngón đảo bao quanh bởi mặt nước yêu cầu thiết bị chống thấm và ngập triệt để.',
                'gallery' => [
                    'storage/media/projects/project-detail-3.jpg',
                    'storage/media/projects/project-villa-park.jpg',
                    'storage/media/projects/project-gallery-2.jpg',
                ],
            ],
            [
                'title' => 'Dinh Thự The River Thủ Thiêm',
                'slug' => 'penthouse-the-river-thu-thiem',
                'excerpt' => 'Dinh thự ven sông cao cấp với hệ thống điện thông minh KNX chuẩn châu Âu và giải pháp lưu trữ năng lượng pin Lithium.',
                'content' => '<p>Tọa lạc tại vị trí độc tôn ven sông Sài Gòn, Dinh Thự The River Thủ Thiêm yêu cầu nguồn điện chất lượng cao không bị biến dạng điện áp. Đại Phúc 68 lắp đặt hệ thống tủ điện phân phối thông minh, bộ lọc sóng hài và hệ thống lưu điện thông minh tích hợp BMS.</p>',
                'featured_image' => 'storage/media/projects/project-detail-4.jpg',
                'category' => 'Nhà Ở & Biệt Thự',
                'category_slug' => 'nha-o-dan-dung',
                'client' => 'Refico Real Estate',
                'location' => 'Khu đô thị mới Thủ Thiêm, Quận 2, TP. HCM',
                'scale' => 'Dinh thự 850 m² - Nguồn điện ổn định 3 pha 100A',
                'year' => '2024',
                'scope' => 'Lắp đặt tủ phân phối thông minh, hệ thống chiếu sáng điều khiển DALI và bộ lọc sóng hài bảo vệ thiết bị hi-end.',
                'challenges' => 'Tiêu chuẩn độ ồn cực thấp và quản lý phụ tải tự động cho hệ thống điều hòa trung tâm VRV.',
                'gallery' => [
                    'storage/media/projects/project-detail-4.jpg',
                    'storage/media/projects/project_showcase_1.jpg',
                    'storage/media/projects/project-detail-1.jpg',
                ],
            ],
            [
                'title' => 'Khu Nhà Phố Manhattan Vinhomes Grand Park',
                'slug' => 'khu-nha-pho-vinhomes-grand-park',
                'excerpt' => 'Đại đô thị thông minh với mạng lưới phân phối điện ngầm hóa đồng bộ, trạm biến áp Kiosk công suất lớn.',
                'content' => '<p>Khu nhà phố Manhattan Vinhomes Grand Park gồm hàng trăm căn shophouse kinh doanh sầm uất. Đại Phúc 68 bàn giao các gói vật tư đầu cáp co nguội 3M, trạm biến áp phân phối hạ thế và tủ điện tổng phục vụ liên tục cho toàn bộ khu thương mại dịch vụ.</p>',
                'featured_image' => 'storage/media/projects/project_showcase_1.jpg',
                'category' => 'Nhà Ở & Biệt Thự',
                'category_slug' => 'nha-o-dan-dung',
                'client' => 'Tập đoàn Vingroup',
                'location' => 'Long Thạnh Mỹ, TP. Thủ Đức, TP. HCM',
                'scale' => 'Phân khu 550 căn nhà phố thương mại & Shophouse',
                'year' => '2024',
                'scope' => 'Cung ứng vật tư cáp ngầm trung thế, đầu cáp co nguội 3M và trạm Kiosk 1000kVA.',
                'challenges' => 'Tiến độ thần tốc bàn giao đồng loạt và yêu cầu phối hợp hạ tầng giao thông ngầm phức tạp.',
                'gallery' => [
                    'storage/media/projects/project_showcase_1.jpg',
                    'storage/media/projects/project-item-6.jpg',
                    'storage/media/projects/project-detail-3.jpg',
                ],
            ],
        ];

        foreach ($projectsData as $prjData) {
            $post = $savePost(
                [
                    'slug' => $prjData['slug'],
                    'post_type' => 'project',
                ],
                [
                    'title' => $prjData['title'],
                    'excerpt' => $prjData['excerpt'],
                    'content' => $prjData['content'],
                    'featured_image' => $prjData['featured_image'],
                    'status' => 'published',
                    'published_at' => now(),
                    'author_id' => $authorId,
                    'project_id' => $projectId,
                    'meta_data' => [
                        'category' => $prjData['category'],
                        'client' => $prjData['client'],
                        'location' => $prjData['location'],
                        'scale' => $prjData['scale'],
                        'year' => $prjData['year'],
                        'scope' => $prjData['scope'],
                        'challenges' => $prjData['challenges'],
                        'gallery' => $prjData['gallery'],
                    ],
                ]
            );

            $projTaxId = $taxMap['proj_' . $prjData['category_slug']] ?? null;
            if ($projTaxId) {
                DB::table('term_relationships')->updateOrInsert(
                    ['object_id' => $post->id, 'term_taxonomy_id' => $projTaxId],
                    ['order' => 0]
                );
            }
        }

        // Clean up and ensure all project posts are published and linked to taxonomies
        Post::where('post_type', 'project')->where('status', 'draft')->update(['status' => 'published']);
        $allProjectsInDb = Post::where('post_type', 'project')->get();
        foreach ($allProjectsInDb as $pr) {
            $hasTerm = DB::table('term_relationships')->where('object_id', $pr->id)->exists();
            if (!$hasTerm) {
                $catName = $pr->meta_data['category'] ?? '';
                $foundTax = Taxonomy::where('taxonomy', 'project_cat')
                    ->where(function($q) use ($catName) {
                        $q->where('name', $catName)->orWhere('slug', Str::slug($catName));
                    })->first();
                if (!$foundTax) {
                    $foundTax = Taxonomy::where('taxonomy', 'project_cat')->where('slug', 'nha-o-dan-dung')->first();
                }
                if ($foundTax) {
                    DB::table('term_relationships')->updateOrInsert(
                        ['object_id' => $pr->id, 'term_taxonomy_id' => $foundTax->id],
                        ['order' => 0]
                    );
                    $meta = $pr->meta_data ?: [];
                    $meta['category'] = $foundTax->name;
                    $pr->update(['meta_data' => $meta]);
                }
            }
        }

        // 5. NEWS / BLOG ARTICLES SEEDING
        $newsData = [
            [
                'title' => 'Đại Phúc 68 bàn giao gói thiết bị trạm biến áp phân phối đạt chuẩn EVN',
                'slug' => 'dai-phuc-68-ban-giao-tram-bien-ap-dat-chuan-evn',
                'excerpt' => 'Vừa qua, Đại Phúc 68 đã hoàn tất công tác cung ứng và nghiệm thu xuất xưởng gói thiết bị trạm biến áp 1000kVA cho dự án công nghiệp trọng điểm.',
                'content' => '<p>Trong bối cảnh nhu cầu tiêu thụ điện năng công nghiệp ngày càng tăng cao, việc bảo đảm thiết bị đóng cắt và biến áp đạt chuẩn chất lượng kiểm định EVN đóng vai trò then chốt trong tính bền vững của dự án.</p><p>Đại Phúc 68 với năng lực cung ứng trọn gói từ cầu chì tự rơi FCO, dao cách ly DS đến máy biến áp ngâm dầu đã phối hợp cùng trung tâm kiểm định Quatest và Điện lực địa phương hoàn tất đóng điện an toàn tuyệt đối.</p>',
                'featured_image' => 'storage/media/news/news-item-1.jpg',
                'category' => 'Dự Án Tiêu Biểu',
                'category_slug' => 'du-an-tieu-bieu',
                'author' => 'Ban Kỹ Thuật Đại Phúc 68',
                'reading_time' => '5 phút đọc',
            ],
            [
                'title' => 'Hướng dẫn lựa chọn cầu chì tự rơi FCO và chống sét van cho lưới 24kV/35kV',
                'slug' => 'huong-dan-lua-chon-fco-va-chong-set-van',
                'excerpt' => 'Phân tích các tiêu chí kỹ thuật quan trọng khi lựa chọn thiết bị bảo vệ đường dây trung thế: dòng cắt ngắn mạch, khoảng cách rò và vật liệu cách điện Polymer.',
                'content' => '<p>Cầu chì tự rơi (FCO/LBFCO) và chống sét van (LA) là hai thiết bị phòng vệ tuyến đầu cho máy biến áp phân phối. Khi lựa chọn thiết bị, kỹ sư điện cần đặc biệt lưu ý đến các thông số dòng cắt định mức đối xứng/bất đối xứng, cấp điện áp làm việc lớn nhất và khoảng cách rò bề mặt phù hợp với từng vùng khí hậu ô nhiễm.</p>',
                'featured_image' => 'storage/media/news/news-item-2.png',
                'category' => 'Kiến Thức Kỹ Thuật',
                'category_slug' => 'kien-thuc-ky-thuat',
                'author' => 'Kỹ sư Nguyễn Thành Nam',
                'reading_time' => '7 phút đọc',
            ],
            [
                'title' => 'Quy chuẩn mới của EVN về thử nghiệm xuất xưởng thiết bị điện trung thế',
                'slug' => 'quy-chuan-moi-evn-kiem-dinh-thiet-bi-dien',
                'excerpt' => 'Tổng hợp các quy định mới nhất về quy trình thử nghiệm điển hình (Type Test) và thử nghiệm xuất xưởng (Routine Test) đối với khí cụ điện phân phối.',
                'content' => '<p>Nhằm nâng cao độ tin cậy cung cấp điện theo các chỉ số SAIFI và SAIDI, EVN đã ban hành các tiêu chuẩn kỹ thuật nâng cấp về thử nghiệm điện áp xoay chiều tăng cao tần số công nghiệp và thử nghiệm phóng điện cục bộ đối với thiết bị trung áp.</p>',
                'featured_image' => 'storage/media/news/news-item-3.jpg',
                'category' => 'Tin Tức Ngành Điện',
                'category_slug' => 'tin-tuc-nganh-dien',
                'author' => 'Phòng Pháp Chế & Tiêu Chuẩn',
                'reading_time' => '4 phút đọc',
            ],
            [
                'title' => 'Đại Phúc 68 ký kết hợp tác chiến lược cùng Vina Electric mở rộng phân phối',
                'slug' => 'dai-phuc-68-hop-tac-chien-luoc-vina-electric',
                'excerpt' => 'Lễ ký kết thỏa thuận hợp tác toàn diện giữa Công ty TNHH Đại Phúc 68 và Nhà máy sản xuất thiết bị điện Vina Electric mở ra bước tiến mới.',
                'content' => '<p>Thỏa thuận hợp tác chiến lược khẳng định vị thế của Đại Phúc 68 là nhà phân phối chính thức cấp 1 các sản phẩm FCO, LBFCO, dao cách ly và sứ cách điện Polymer của Vina Electric trên phạm vi toàn quốc, cam kết chính sách giá và bảo hành tối ưu nhất cho nhà thầu cơ điện.</p>',
                'featured_image' => 'storage/media/news/news-item-4.jpg',
                'category' => 'Thông Báo Doanh Nghiệp',
                'category_slug' => 'thong-bao-doanh-nghiep',
                'author' => 'Ban Giám Đốc Đại Phúc 68',
                'reading_time' => '3 phút đọc',
            ],
            [
                'title' => 'Giải pháp chống sét lan truyền và tối ưu độ tin cậy lưới điện mùa mưa bão',
                'slug' => 'giai-phap-chong-set-lan-truyen-luoi-dien',
                'excerpt' => 'Các kinh nghiệm thực chiến giúp ngăn ngừa sự cố sét đánh lan truyền gây hỏng cuộn dây máy biến áp và cháy cầu chì tự rơi.',
                'content' => '<p>Vào mùa giông sét cao điểm tại Việt Nam, mật độ sét đánh trên mỗi km² tăng vọt. Bài viết chia sẻ giải pháp phối hợp bảo vệ giữa chống sét van Polymer Oxit kim loại ZnO không khe hở và hệ thống tiếp địa điện trở dưới 4 Ohm theo tiêu chuẩn ngành điện.</p>',
                'featured_image' => 'storage/media/news/news-item-5.jpg',
                'category' => 'Kiến Thức Kỹ Thuật',
                'category_slug' => 'kien-thuc-ky-thuat',
                'author' => 'Ban Kỹ Thuật Đại Phúc 68',
                'reading_time' => '6 phút đọc',
            ],
            [
                'title' => 'Ứng dụng ống trụ bê tông ly tâm dự ứng lực trong xây lắp trạm biến áp treo',
                'slug' => 'ung-dung-ong-tru-be-tong-ly-tam-xay-lap-tram',
                'excerpt' => 'Đánh giá khả năng chịu tải cơ học và độ bền công trình khi sử dụng cột bê tông ly tâm mác cao trong các trạm giàn và trạm treo phân phối.',
                'content' => '<p>Cột bê tông ly tâm dự ứng lực giúp giảm trọng lượng thân cột, gia tăng khả năng chống uốn nứt khi đặt máy biến áp công suất lớn và xà đỡ FCO trên đỉnh cột, rút ngắn thời gian thi công dựng trạm đến 40% so với phương pháp truyền thống.</p>',
                'featured_image' => 'storage/media/news/news-item-6.jpg',
                'category' => 'Kiến Thức Kỹ Thuật',
                'category_slug' => 'kien-thuc-ky-thuat',
                'author' => 'Kỹ sư Lê Hoàng Anh',
                'reading_time' => '5 phút đọc',
            ],
        ];

        foreach ($newsData as $nData) {
            $post = $savePost(
                [
                    'slug' => $nData['slug'],
                    'post_type' => 'post',
                ],
                [
                    'title' => $nData['title'],
                    'excerpt' => $nData['excerpt'],
                    'content' => $nData['content'],
                    'featured_image' => $nData['featured_image'],
                    'status' => 'published',
                    'published_at' => now(),
                    'author_id' => $authorId,
                    'project_id' => $projectId,
                    'meta_data' => [
                        'author' => $nData['author'],
                        'reading_time' => $nData['reading_time'],
                        'category' => $nData['category'],
                    ],
                ]
            );

            $newsTaxId = $taxMap['news_' . $nData['category_slug']] ?? null;
            if ($newsTaxId) {
                DB::table('term_relationships')->updateOrInsert(
                    ['object_id' => $post->id, 'term_taxonomy_id' => $newsTaxId],
                    ['order' => 0]
                );
            }
        }

        // 6. STATIC / SYSTEM PAGES SEEDING
        $pagesData = [
            [
                'title' => 'Về Chúng Tôi | Đại Phúc 68',
                'slug' => 'about-us',
                'excerpt' => 'Với hơn 10 năm kinh nghiệm hoạt động trong lĩnh vực vật tư thiết bị điện và xây lắp cơ điện, Đại Phúc 68 tự hào mang đến giải pháp trọn gói chất lượng cao, bền vững theo thời gian.',
                'content' => 'Công ty TNHH Đại Phúc 68 là đơn vị chuyên phân phối thiết bị điện công nghiệp, thiết bị trạm biến áp và giải pháp cơ điện hàng đầu Việt Nam.',
                'featured_image' => 'storage/media/banners/aboutus_banner.png',
            ],
            [
                'title' => 'Liên Hệ | Đại Phúc 68',
                'slug' => 'contact',
                'excerpt' => 'Thông tin liên hệ Công ty TNHH Đại Phúc 68 - Trụ sở 67 Huỳnh Thiện Lộc, P. Tân Phú, TP. Hồ Chí Minh.',
                'content' => 'Liên hệ Công ty TNHH Đại Phúc 68 để nhận báo giá dự án và tư vấn kỹ thuật chi tiết.',
                'featured_image' => 'storage/media/banners/hero-banner-main.jpg',
            ],
            [
                'title' => 'Danh Mục Sản Phẩm & Thiết Bị Điện',
                'slug' => 'products',
                'excerpt' => 'Đại Phúc 68 chuyên cung cấp vật tư thiết bị điện trung thế, hạ thế, máy biến áp và phụ kiện lưới điện chính hãng đạt chuẩn EVN.',
                'content' => 'Danh mục sản phẩm thiết bị điện của Đại Phúc 68.',
                'featured_image' => 'storage/media/banners/hero-banner-main.jpg',
            ],
            [
                'title' => 'Dự Án Công Trình Tiêu Biểu',
                'slug' => 'projects',
                'excerpt' => 'Tổng hợp các dự án trạm biến áp, mạng lưới điện ngầm và công trình kiến trúc Đại Phúc 68 đã thực hiện.',
                'content' => 'Danh mục dự án tiêu biểu.',
                'featured_image' => 'storage/media/projects/project-item-1.png',
            ],
            [
                'title' => 'Tin Tức & Hoạt Động Doanh Nghiệp',
                'slug' => 'news',
                'excerpt' => 'Cập nhật tin tức thị trường điện năng, kiến thức kỹ thuật và sự kiện nội bộ mới nhất từ Đại Phúc 68.',
                'content' => 'Tin tức và hoạt động doanh nghiệp.',
                'featured_image' => 'storage/media/news/news-item-1.jpg',
            ],
        ];

        foreach ($pagesData as $pgData) {
            $savePost(
                [
                    'slug' => $pgData['slug'],
                    'post_type' => 'page',
                ],
                [
                    'title' => $pgData['title'],
                    'excerpt' => $pgData['excerpt'],
                    'content' => $pgData['content'],
                    'featured_image' => $pgData['featured_image'],
                    'status' => 'published',
                    'published_at' => now(),
                    'author_id' => $authorId,
                    'project_id' => $projectId,
                ]
            );
        }

        // 9. CÂU HỎI THƯỜNG GẶP (FAQ: Phục vụ trang /contact)
        $faqsData = [
            [
                'order' => 1,
                'title' => 'Câu hỏi số 1: Đại Phúc 68 cung cấp những dịch vụ & giải pháp nào?',
                'slug' => 'cau-hoi-so-1-dai-phuc-68-cung-cap-nhung-dich-vu-giai-phap-nao',
                'content' => 'Đại Phúc 68 chuyên cung ứng vật tư thiết bị điện trung thế, hạ thế, trạm biến áp, tư vấn thiết kế kiến trúc kỹ thuật và thi công xây lắp trọn gói.',
                'excerpt' => 'Đại Phúc 68 chuyên cung ứng vật tư thiết bị điện trung thế, hạ thế, trạm biến áp, tư vấn thiết kế kiến trúc kỹ thuật và thi công xây lắp trọn gói.',
            ],
            [
                'order' => 2,
                'title' => 'Câu hỏi số 2: Chính sách bảo hành và cam kết chất lượng dự án?',
                'slug' => 'cau-hoi-so-2-chinh-sach-bao-hanh-va-cam-ket-chat-luong-du-an',
                'content' => 'Toàn bộ vật tư và công trình được bảo hành chính hãng từ 12 đến 36 tháng kèm dịch vụ hỗ trợ kỹ thuật bảo trì định kỳ 24/7.',
                'excerpt' => 'Toàn bộ vật tư và công trình được bảo hành chính hãng từ 12 đến 36 tháng kèm dịch vụ hỗ trợ kỹ thuật bảo trì định kỳ 24/7.',
            ],
            [
                'order' => 3,
                'title' => 'Câu hỏi số 3: Quy trình tiếp nhận yêu cầu báo giá diễn ra bao lâu?',
                'slug' => 'cau-hoi-so-3-quy-trinh-tiep-nhan-yeu-cau-bao-gia-dien-ra-bao-lau',
                'content' => 'Đội ngũ kỹ sư sẽ khảo sát, tư vấn giải pháp sơ bộ và gửi bảng khái toán chi tiết trong vòng 24 giờ làm việc.',
                'excerpt' => 'Đội ngũ kỹ sư sẽ khảo sát, tư vấn giải pháp sơ bộ và gửi bảng khái toán chi tiết trong vòng 24 giờ làm việc.',
            ],
            [
                'order' => 4,
                'title' => 'Câu hỏi số 4: Công ty có nhận cung ứng vật tư trên toàn quốc không?',
                'slug' => 'cau-hoi-so-4-cong-ty-co-nhan-cung-ung-vat-tu-tren-toan-quoc-khong',
                'content' => 'Có, hệ thống kho bãi và đối tác vận tải của chúng tôi phục vụ giao hàng chuẩn tiến độ trên toàn bộ 63 tỉnh thành Việt Nam.',
                'excerpt' => 'Có, hệ thống kho bãi và đối tác vận tải của chúng tôi phục vụ giao hàng chuẩn tiến độ trên toàn bộ 63 tỉnh thành Việt Nam.',
            ],
            [
                'order' => 5,
                'title' => 'Câu hỏi số 5: Thiết bị điện trung thế có đầy đủ chứng chỉ CO/CQ không?',
                'slug' => 'cau-hoi-so-5-thiet-bi-dien-trung-the-co-day-du-chung-chi-co-cq-khong',
                'content' => '100% thiết bị nhập khẩu và phân phối đều có đầy đủ chứng chỉ xuất xứ (CO), chứng chỉ chất lượng (CQ) và biên bản thử nghiệm đạt chuẩn.',
                'excerpt' => '100% thiết bị nhập khẩu và phân phối đều có đầy đủ chứng chỉ xuất xứ (CO), chứng chỉ chất lượng (CQ) và biên bản thử nghiệm đạt chuẩn.',
            ],
            [
                'order' => 6,
                'title' => 'Câu hỏi số 6: Hồ sơ năng lực công ty bao gồm những công trình tiêu biểu nào?',
                'slug' => 'cau-hoi-so-6-ho-so-nang-luc-cong-ty-bao-gom-nhung-cong-trinh-tieu-bieu-nao',
                'content' => 'Chúng tôi đã thực hiện trên 150+ dự án trạm biến áp, nhà xưởng công nghiệp, khu dân cư cao cấp và hạ tầng điện lưới đô thị.',
                'excerpt' => 'Chúng tôi đã thực hiện trên 150+ dự án trạm biến áp, nhà xưởng công nghiệp, khu dân cư cao cấp và hạ tầng điện lưới đô thị.',
            ],
            [
                'order' => 7,
                'title' => 'Câu hỏi số 7: Chi phí tư vấn thiết kế ban đầu có được miễn phí không?',
                'slug' => 'cau-hoi-so-7-chi-phi-tu-van-thiet-ke-ban-dau-co-duoc-mien-phi-khong',
                'content' => 'Đối với khách hàng ký hợp đồng thi công trọn gói, Đại Phúc 68 hỗ trợ 100% chi phí khảo sát và lập bản vẽ thiết kế kỹ thuật.',
                'excerpt' => 'Đối với khách hàng ký hợp đồng thi công trọn gói, Đại Phúc 68 hỗ trợ 100% chi phí khảo sát và lập bản vẽ thiết kế kỹ thuật.',
            ],
            [
                'order' => 8,
                'title' => 'Câu hỏi số 8: Phương thức thanh toán và bảo lãnh thực hiện hợp đồng?',
                'slug' => 'cau-hoi-so-8-phuong-thuc-thanh-toan-va-bao-lanh-thuc-hien-hop-dong',
                'content' => 'Thanh toán linh hoạt theo từng giai đoạn nghiệm thu công trình, hỗ trợ phát hành bảo lãnh tạm ứng và bảo lãnh bảo hành qua ngân hàng uy tín.',
                'excerpt' => 'Thanh toán linh hoạt theo từng giai đoạn nghiệm thu công trình, hỗ trợ phát hành bảo lãnh tạm ứng và bảo lãnh bảo hành qua ngân hàng uy tín.',
            ],
            [
                'order' => 9,
                'title' => 'Câu hỏi số 9: Thời gian thi công lắp đặt trạm biến áp trung bình mất bao lâu?',
                'slug' => 'cau-hoi-so-9-thoi-gian-thi-cong-lap-dat-tram-bien-ap-trung-binh-mat-bao-lau',
                'content' => 'Tùy quy mô trạm từ 100kVA đến 2500kVA, thời gian thi công hoàn thiện và đóng điện kiểm định dao động từ 15 đến 45 ngày làm việc.',
                'excerpt' => 'Tùy quy mô trạm từ 100kVA đến 2500kVA, thời gian thi công hoàn thiện và đóng điện kiểm định dao động từ 15 đến 45 ngày làm việc.',
            ],
            [
                'order' => 10,
                'title' => 'Câu hỏi số 10: Có hỗ trợ thủ tục thỏa thuận đấu nối với Điện lực (EVN) không?',
                'slug' => 'cau-hoi-so-10-co-ho-tro-thu-tuc-thoa-thuan-dau-noi-voi-dien-luc-evn-khong',
                'content' => 'Có, chúng tôi hỗ trợ trọn gói từ khâu lập hồ sơ xin đấu nối, thẩm định thiết kế đến nghiệm thu đóng điện cùng EVN khu vực.',
                'excerpt' => 'Có, chúng tôi hỗ trợ trọn gói từ khâu lập hồ sơ xin đấu nối, thẩm định thiết kế đến nghiệm thu đóng điện cùng EVN khu vực.',
            ],
            [
                'order' => 11,
                'title' => 'Câu hỏi số 11: Đại Phúc 68 cung cấp những thương hiệu thiết bị điện nào?',
                'slug' => 'cau-hoi-so-11-dai-phuc-68-cung-cap-nhung-thuong-hieu-thiet-bi-dien-nao',
                'content' => 'Chúng tôi là đối tác phân phối chính thức của ABB, Schneider Electric, Siemens, LS, CADIVI, Vina Electric và các thương hiệu hàng đầu.',
                'excerpt' => 'Chúng tôi là đối tác phân phối chính thức của ABB, Schneider Electric, Siemens, LS, CADIVI, Vina Electric và các thương hiệu hàng đầu.',
            ],
            [
                'order' => 12,
                'title' => 'Câu hỏi số 12: Đội ngũ kỹ sư và công nhân có chứng chỉ an toàn lao động không?',
                'slug' => 'cau-hoi-so-12-doi-ngu-ky-su-va-cong-nhan-co-chung-chi-an-toan-lao-dong-khong',
                'content' => '100% kỹ sư và công nhân thi công đều được đào tạo định kỳ và sở hữu chứng chỉ an toàn lao động, an toàn điện theo quy chuẩn Bộ Công Thương.',
                'excerpt' => '100% kỹ sư và công nhân thi công đều được đào tạo định kỳ và sở hữu chứng chỉ an toàn lao động, an toàn điện theo quy chuẩn Bộ Công Thương.',
            ],
        ];

        foreach ($faqsData as $faqItem) {
            $savePost(
                [
                    'slug' => $faqItem['slug'],
                    'post_type' => 'faq',
                ],
                [
                    'title' => $faqItem['title'],
                    'excerpt' => $faqItem['excerpt'],
                    'content' => $faqItem['content'],
                    'order' => $faqItem['order'],
                    'meta_data' => ['order' => $faqItem['order']],
                    'status' => 'published',
                    'published_at' => now(),
                    'author_id' => $authorId,
                    'project_id' => $projectId,
                ]
            );
        }
    }
}
