<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $authorId = $user ? $user->id : 1;

        $project = Project::where('code', 'daiphuc68')->first();
        $projectId = $project ? $project->id : null;
        $tenantId = $project ? $project->tenant_id : null;

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
            Post::updateOrCreate(
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
