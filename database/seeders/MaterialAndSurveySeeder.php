<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialAndSurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. SEED DEFAULT SURVEY: 02_online_KHAO SAT KH
        // ==========================================
        $survey = Survey::firstOrCreate(
            ['code' => 'online_khao_sat_kh'],
            [
                'title' => 'KHẢO SÁT HÀI LÒNG',
                'subtitle' => 'WORKSPACE®',
                'intro_note' => 'Kính chào Quý Khách hàng, nhằm không ngừng nâng cao chất lượng dịch vụ, chúng tôi kính mong Quý vị dành ít phút đánh giá về chất lượng công trình và dịch vụ của chúng tôi.',
                'thank_you_note' => 'Cảm ơn Quý Khách hàng đã tin tưởng và đồng hành cùng chúng tôi',
                'footer_text' => 'Trân trọng cảm ơn Quý Khách hàng!',
                'is_active' => true,
                'settings' => [
                    'scale_guide_title' => 'Hướng dẫn chấm điểm (Thang 1-5)',
                    'scales' => [
                        ['val' => 1, 'label' => '1 - Rất kém:', 'desc' => 'Không đạt, cần khắc phục ngay'],
                        ['val' => 2, 'label' => '2 - Kém:', 'desc' => 'Dưới kỳ vọng'],
                        ['val' => 3, 'label' => '3 - Trung bình:', 'desc' => 'Đạt yêu cầu cơ bản'],
                        ['val' => 4, 'label' => '4 - Tốt:', 'desc' => 'Vượt kỳ vọng'],
                        ['val' => 5, 'label' => '5 - Xuất sắc:', 'desc' => 'Vượt trội, ấn tượng'],
                    ],
                ],
            ]
        );

        $questionsA = [
            ['code' => 'A1', 'text' => 'Tiến độ thi công đúng cam kết?'],
            ['code' => 'A2', 'text' => 'Chất lượng thi công, độ hoàn thiện chi tiết?'],
            ['code' => 'A3', 'text' => 'Thái độ, sự chuyên nghiệp của đội ngũ giám sát?'],
            ['code' => 'A4', 'text' => 'Vệ sinh công trình sau thi công?'],
            ['code' => 'A5', 'text' => 'Mức độ đáp ứng yêu cầu thiết kế ban đầu?'],
            ['code' => 'A6', 'text' => 'Sự rõ ràng về chi phí, hợp đồng?'],
        ];

        foreach ($questionsA as $index => $q) {
            SurveyQuestion::firstOrCreate(
                ['survey_id' => $survey->id, 'code' => $q['code']],
                [
                    'section' => 'A',
                    'question_text' => $q['text'],
                    'question_type' => 'rating_5',
                    'is_required' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        $questionsB = [
            ['code' => 'B1', 'text' => 'Tốc độ phản hồi khi có yêu cầu bảo hành?'],
            ['code' => 'B2', 'text' => 'Hiệu quả xử lý vấn đề kỹ thuật?'],
            ['code' => 'B3', 'text' => 'Công tác vệ sinh trong quá trình bảo hành?'],
            ['code' => 'B4', 'text' => 'Thái độ hỗ trợ của nhân sự bảo hành?'],
        ];

        foreach ($questionsB as $index => $q) {
            SurveyQuestion::firstOrCreate(
                ['survey_id' => $survey->id, 'code' => $q['code']],
                [
                    'section' => 'B',
                    'question_text' => $q['text'],
                    'question_type' => 'rating_5',
                    'is_required' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        SurveyQuestion::firstOrCreate(
            ['survey_id' => $survey->id, 'code' => 'B5'],
            [
                'section' => 'NPS',
                'question_text' => 'Anh/Chị có sẵn sàng giới thiệu Workspace cho bạn bè / đối tác?',
                'question_type' => 'rating_10_nps',
                'is_required' => true,
                'sort_order' => 11,
            ]
        );

        SurveyQuestion::firstOrCreate(
            ['survey_id' => $survey->id, 'code' => 'OPEN_1'],
            [
                'section' => 'OPEN',
                'question_text' => 'Điều chúng tôi làm tốt nhất là gì?',
                'question_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 12,
            ]
        );

        SurveyQuestion::firstOrCreate(
            ['survey_id' => $survey->id, 'code' => 'OPEN_2'],
            [
                'section' => 'OPEN',
                'question_text' => 'Điều chúng tôi cần cải thiện?',
                'question_type' => 'textarea',
                'is_required' => false,
                'sort_order' => 13,
            ]
        );

        // ==========================================
        // 2. SEED DEFAULT 15 MATERIALS: Material-Lab
        // ==========================================
        $materialsData = [
            [
                'code' => 'lvt',
                'name' => 'LVT Moduleo 55',
                'brand' => 'Moduleo / Belgium',
                'suitable_locations' => ['Floor'],
                'category' => 'Vinyl',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – Bỉ',
                'availability' => '2-3 weeks',
                'durability_years' => 10,
                'cost_label' => '450k/m2',
                'budget_level' => 3,
                'finish' => 'Vân gỗ mờ',
                'tech_specs' => 'R10, Class 33',
                'application' => 'Chịu mài mòn AC5, tiêu âm 18dB',
                'tech_description' => 'Theo TCVN 8264:2009 và EN 14041, LVT Class 33 AC5 đáp ứng tải trọng văn phòng 5kN/m². R10 phù hợp WELL v2 Feature S03. Lưu ý QCVN 06:2022 – khu vực ẩm cần R11 trở lên.',
                'experience_description' => '23 phòng họp dùng Moduleo 55 – ấm chân, tiêu âm 18dB tốt. Lưu ý 2 case tầng 1 ẩm 78% bị phồng mép sau 14 tháng. Cần nền phẳng <3mm/2m, ẩm <75%, keo Wakol D3310, chừa khe 5mm.',
                'sort_order' => 1,
            ],
            [
                'code' => 'terrazzo',
                'name' => 'Terrazzo Precast 60×60',
                'brand' => 'Vietbeton',
                'suitable_locations' => ['Floor'],
                'category' => 'Terrazzo',
                'origin' => 'Vietnam',
                'origin_label' => 'Hàng Việt – Bình Dương',
                'availability' => 'Stock',
                'durability_years' => 15,
                'cost_label' => '620k/m2',
                'budget_level' => 3,
                'finish' => 'Mài bóng lộ đá',
                'tech_specs' => 'R11, 40MPa',
                'application' => 'Lưu thông rất cao',
                'tech_description' => 'QCVN 16:2023 – Terrazzo precast nén 40MPa vượt TCVN 7742. R11 đạt QCVN 06:2022 cho hành lang thoát nạn. WELL v2 khuyến khích bề mặt mài lộ đá không VOC.',
                'experience_description' => '12 sảnh dùng Terrazzo – siêu bền 15 năm, xe pallet kéo không xước, mài lại được sau 7 năm. Giá 620k/m2 cao hơn gạch nhưng không cần thay thế.',
                'sort_order' => 2,
            ],
            [
                'code' => 'porcelain',
                'name' => 'Porcelain Taicera GP-03',
                'brand' => 'Taicera / Vietnam',
                'suitable_locations' => ['Floor', 'Wall'],
                'category' => 'Porcelain',
                'origin' => 'Vietnam',
                'origin_label' => 'Hàng Việt – Có sẵn',
                'availability' => 'Stock',
                'durability_years' => 15,
                'cost_label' => '275k/m2',
                'budget_level' => 2,
                'finish' => 'Giả đá mờ',
                'tech_specs' => 'PEI 4, <0.5% abs',
                'application' => 'Chống trượt R11',
                'tech_description' => 'QCVN 06:2022/BXD yêu cầu sàn lối thoát nạn không trơn trượt. R11 + PEI4 + hấp thụ <0.5% đạt QCVN 16:2023. LEED v4.1 low-emitting không yêu cầu VOC cho gạch nung.',
                'experience_description' => 'Workspace đã làm 41 công trình văn phòng với Taicera GP-03 – bền 8 năm không sứt mẻ. Tồn kho Hà Nội luôn sẵn 4000m2, mạch 2mm keo Weber. Nền ẩm >80% vẫn ổn, không phồng như LVT.',
                'sort_order' => 3,
            ],
            [
                'code' => 'laminate-hpl',
                'name' => 'Laminate HPL Formica',
                'brand' => 'Formica / USA',
                'suitable_locations' => ['Wall', 'Detail Surface'],
                'category' => 'HPL',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – USA',
                'availability' => '2-3 weeks',
                'durability_years' => 7,
                'cost_label' => '385k/m2',
                'budget_level' => 2,
                'finish' => 'Mờ mịn chống vân tay',
                'tech_specs' => 'Fire B1',
                'application' => 'Chống va đập',
                'tech_description' => 'EN 438 Fire B1, kháng khuẩn ISO 22196. QCVN 16:2023 cho tấm HPL trong hành lang. WELL X06 VOC <0.05mg/m³ formaldehyde.',
                'experience_description' => 'HPL Formica – 22 tủ bếp pantry + vách WC, chống va đập, chống nước cạnh dán PUR. Giá 385k/m2, tồn 2-3 tuần.',
                'sort_order' => 4,
            ],
            [
                'code' => 'kvadrat',
                'name' => 'Kvadrat Divina Melange',
                'brand' => 'Kvadrat / Denmark',
                'suitable_locations' => ['Loose Furniture'],
                'category' => 'Wool',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – Đan Mạch',
                'availability' => '6-8 weeks',
                'durability_years' => 5,
                'cost_label' => '890k/m',
                'budget_level' => 4,
                'finish' => 'Dệt nỉ len',
                'tech_specs' => 'Wool 100%',
                'application' => 'Bọc ghế, tiêu âm',
                'tech_description' => 'Bề mặt chi tiết – Wool 100%. LEED v4.1 EQ + WELL X – Bọc ghế, tiêu âm. Kiểm soát formaldehyde E0/FSC.',
                'experience_description' => 'Kvadrat Divina – 11 lounge bọc ghế, len 100% Đan Mạch, Martindale 45k bền 5 năm, nhưng 890k/m, cần giặt khô.',
                'sort_order' => 5,
            ],
            [
                'code' => 'dulux',
                'name' => 'Dulux Ambiance 5-in-1',
                'brand' => 'Dulux / AkzoNobel VN',
                'suitable_locations' => ['Wall', 'Ceiling'],
                'category' => 'Paint',
                'origin' => 'Vietnam',
                'origin_label' => 'Hàng Việt – Pha tại đại lý',
                'availability' => 'Stock',
                'durability_years' => 5,
                'cost_label' => '85k/m2',
                'budget_level' => 1,
                'finish' => 'Sơn mờ siêu mịn',
                'tech_specs' => 'VOC <10g/L',
                'application' => 'Lau chùi dễ',
                'tech_description' => 'QCVN 16:2023 giới hạn VOC <50g/L – Dulux Ambiance <10g/L vượt LEED v4.1 EQ Low-Emitting và WELL A06 VOC. Khả năng lau chùi >5000 lần đạt TCVN 8652.',
                'experience_description' => 'Dulux 5-in-1 – 100+ công trình dùng, pha tại đại lý 30 phút, dặm vá dễ. Mẹo: 1 lót + 2 phủ, định mức 12m2/lít, rulo 12mm cho mặt mờ mịn.',
                'sort_order' => 6,
            ],
            [
                'code' => 'armstrong',
                'name' => 'Armstrong Ultima+',
                'brand' => 'Armstrong / UK',
                'suitable_locations' => ['Ceiling'],
                'category' => 'Mineral',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – Anh',
                'availability' => '2-3 weeks',
                'durability_years' => 10,
                'cost_label' => '320k/m2',
                'budget_level' => 2,
                'finish' => 'Micro-perforated',
                'tech_specs' => 'NRC 0.70',
                'application' => 'Chống ẩm RH99',
                'tech_description' => 'Trần yêu cầu Fire Class A/B – NRC 0.70. NRC 0.65-0.70 đạt WELL S04 và LEED acoustic. RH99 chống ẩm phù hợp TCVN 4088:1985 khí hậu nóng ẩm VN.',
                'experience_description' => 'Armstrong Ultima+ – 27 trần văn phòng, NRC 0.70 thực tế, ẩm 99% không võng, tháo tấm bảo trì dễ. Hệ xương T-24 quen thuộc.',
                'sort_order' => 7,
            ],
            [
                'code' => 'glass-lowe',
                'name' => 'Glass Low-E 8mm',
                'brand' => 'Saint-Gobain',
                'suitable_locations' => ['Wall', 'Detail Surface'],
                'category' => 'Glass',
                'origin' => 'Both',
                'origin_label' => 'Nhập phôi, gia công VN',
                'availability' => '2-3 weeks',
                'durability_years' => 15,
                'cost_label' => '750k/m2',
                'budget_level' => 3,
                'finish' => 'Clear Low-Iron',
                'tech_specs' => 'U 1.8',
                'application' => 'Cách nhiệt 68%',
                'tech_description' => 'Vật liệu ốp tường hành lang yêu cầu Fire B1. U 1.8 đáp ứng TCVN 7890. Finish Clear Low-Iron độ phản xạ 45-65%.',
                'experience_description' => 'Saint-Gobain Low-E 8mm – 14 vách meeting, cách nhiệt U1.8 giảm 30% điều hòa, không chói. Gia công Hải Dương 7 ngày.',
                'sort_order' => 8,
            ],
            [
                'code' => 'corian',
                'name' => 'Corian Solid Surface',
                'brand' => 'DuPont / USA',
                'suitable_locations' => ['Detail Surface', 'Wall'],
                'category' => 'Solid Surface',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – USA',
                'availability' => '6-8 weeks',
                'durability_years' => 15,
                'cost_label' => '1.1tr/m2',
                'budget_level' => 4,
                'finish' => 'Liền khối',
                'tech_specs' => 'NSF kháng khuẩn',
                'application' => 'Uốn cong nhiệt',
                'tech_description' => 'Bề mặt chi tiết – NSF kháng khuẩn. LEED v4.1 EQ + WELL X – Uốn cong nhiệt. Kiểm soát formaldehyde E0/FSC.',
                'experience_description' => 'Corian – 8 quầy lễ tân uốn cong liền khối, kháng khuẩn NSF, nhưng giá 1.1tr/m2 cao, chờ 6-8 tuần. Xước mờ đánh bóng lại được.',
                'sort_order' => 9,
            ],
            [
                'code' => 'veneer-oak',
                'name' => 'Veneer Oak White Rift FSC',
                'brand' => 'Alpi / An Cường',
                'suitable_locations' => ['Wall', 'Loose Furniture', 'Detail Surface'],
                'category' => 'Veneer',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – Italy FSC',
                'availability' => '6-8 weeks',
                'durability_years' => 10,
                'cost_label' => '1.2tr/m2',
                'budget_level' => 4,
                'finish' => 'Natural Oiled',
                'tech_specs' => 'FSC, E0',
                'application' => 'Vân sọc thẳng',
                'tech_description' => 'Bề mặt chi tiết – FSC, E0. LEED v4.1 EQ + WELL X – Vân sọc thẳng. Kiểm soát formaldehyde E0/FSC.',
                'experience_description' => 'Veneer Oak White Rift FSC – 19 phòng điều hành, vân sọc thẳng sang. Nhập Italy 6-8 tuần, cần kiểm ẩm gỗ <12%, keo E0.',
                'sort_order' => 10,
            ],
            [
                'code' => 'carpet',
                'name' => 'Carpet Interface Composure',
                'brand' => 'Interface / NL',
                'suitable_locations' => ['Floor'],
                'category' => 'Carpet',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – Hà Lan',
                'availability' => 'Stock',
                'durability_years' => 7,
                'cost_label' => '650k/m2',
                'budget_level' => 3,
                'finish' => 'Loop Pile',
                'tech_specs' => '28dB',
                'application' => 'Tiêu âm, carbon neutral',
                'tech_description' => 'TCVN 4474:1987 + LEED acoustic: NRC 0.28 dB absorption đạt WELL S04 Sound Privacy. Yêu cầu Fire B1-s1 theo QCVN 06:2022 cho thảm module.',
                'experience_description' => 'Interface Composure – dùng cho 9 open work zone, tiêu âm 28dB giảm 40% tiếng ồn, carbon neutral. Cần hút bụi 3 lần/tuần, tránh khu pantry.',
                'sort_order' => 11,
            ],
            [
                'code' => 'pet-felt',
                'name' => 'PET Felt 9mm Acoustic',
                'brand' => 'Feltouch / Turkey',
                'suitable_locations' => ['Wall', 'Ceiling'],
                'category' => 'PET',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – Thổ Nhĩ Kỳ',
                'availability' => 'Stock',
                'durability_years' => 7,
                'cost_label' => '420k/m2',
                'budget_level' => 2,
                'finish' => 'Nỉ ép',
                'tech_specs' => 'NRC 0.45',
                'application' => '60% PET tái chế',
                'tech_description' => 'NRC 0.45 theo ASTM C423, 60% PET tái chế đạt LEED MRc4. Fire Class B theo QCVN 06:2022 cho ốp tường tiêu âm phòng họp. WELL S05 khuyến nghị.',
                'experience_description' => 'Feltouch 9mm – 18 phòng họp ốp tiêu âm, giảm vang 0.6s → 0.4s, vân nỉ đẹp. Có sẵn cuộn 1.2x2.4m, cắt CNC logo được.',
                'sort_order' => 12,
            ],
            [
                'code' => 'inox',
                'name' => 'Inox Hairline 304',
                'brand' => 'Posco / Korea-VN',
                'suitable_locations' => ['Detail Surface'],
                'category' => 'Stainless',
                'origin' => 'Vietnam',
                'origin_label' => 'Hàng Việt – Gia công VN',
                'availability' => 'Stock',
                'durability_years' => 15,
                'cost_label' => '560k/m2',
                'budget_level' => 3,
                'finish' => 'Hairline Brushed',
                'tech_specs' => '304',
                'application' => 'Chống ăn mòn',
                'tech_description' => 'Bề mặt chi tiết – 304. LEED v4.1 EQ + WELL X – Chống ăn mòn. Kiểm soát formaldehyde E0/FSC.',
                'experience_description' => 'Inox hairline 304 – 34 tay vịn + chân bàn, không gỉ, chống vân tay, hàn TIG đẹp. Có sẵn tấm 1.2x2.4m.',
                'sort_order' => 13,
            ],
            [
                'code' => 'rubber-nora',
                'name' => 'Rubber Nora Noraplan',
                'brand' => 'Nora by Interface / DE',
                'suitable_locations' => ['Floor'],
                'category' => 'Rubber',
                'origin' => 'Import',
                'origin_label' => 'Nhập khẩu – Đức',
                'availability' => '2-3 weeks',
                'durability_years' => 10,
                'cost_label' => '520k/m2',
                'budget_level' => 3,
                'finish' => 'Đàn hồi',
                'tech_specs' => 'R9, Bfl-s1',
                'application' => 'Giảm chấn, kháng khuẩn',
                'tech_description' => 'DIN 51130 R9 + Bfl-s1 theo QCVN 06:2022 cho lối đi y tế/giáo dục. Kháng khuẩn theo TCVN ISO 22196. Độ đàn hồi 14% giảm chấn theo WELL Movement.',
                'experience_description' => 'Nora Noraplan – 7 trường học + bệnh viện, đàn hồi giảm chấn ngã, kháng khuẩn, Bfl-s1 an toàn. Cần nền siêu phẳng.',
                'sort_order' => 14,
            ],
            [
                'code' => 'gypsum',
                'name' => 'Gypsum Knauf Cleaneo',
                'brand' => 'Knauf / Vietnam-DE',
                'suitable_locations' => ['Ceiling'],
                'category' => 'Gypsum',
                'origin' => 'Vietnam',
                'origin_label' => 'Hàng Việt – Liên doanh',
                'availability' => 'Stock',
                'durability_years' => 10,
                'cost_label' => '180k/m2',
                'budget_level' => 1,
                'finish' => 'Perforated',
                'tech_specs' => 'NRC 0.65',
                'application' => 'Tiêu âm, hút ẩm',
                'tech_description' => 'Trần yêu cầu Fire Class A/B – NRC 0.65. NRC 0.65-0.70 đạt WELL S04 và LEED acoustic. RH99 chống ẩm phù hợp TCVN 4088:1985 khí hậu nóng ẩm VN.',
                'experience_description' => 'Knauf Cleaneo – 35 trần văn phòng + lớp học, tiêu âm + hút formaldehyde, thi công 2 ngày/100m2, tiết kiệm nhất.',
                'sort_order' => 15,
            ],
        ];

        $defaultTypologyKeys = ['Office', 'School', 'Townhouse', 'Coffee Shop', 'Retail', 'Apartment'];

        foreach ($materialsData as $item) {
            if (! isset($item['suitable_typologies'])) {
                $item['suitable_typologies'] = $defaultTypologyKeys;
            }
            if (! isset($item['suitable_areas'])) {
                $item['suitable_areas'] = [];
            }
            Material::updateOrCreate(['code' => $item['code']], $item);
        }

        // ==========================================
        // 3. SEED MATERIAL LAB CONFIGURATION IN SETTINGS
        // ==========================================
        $typologies = [
            ['en' => 'Office', 'vi' => 'Văn phòng làm việc', 'icon' => '🏢', 'desc' => 'Không gian hiệu suất cao, cần tiêu âm và độ bền bề mặt.'],
            ['en' => 'School', 'vi' => 'Trường học', 'icon' => '🎓', 'desc' => 'An toàn, kháng khuẩn, bền và dễ bảo trì.'],
            ['en' => 'Townhouse', 'vi' => 'Nhà phố', 'icon' => '🏠', 'desc' => 'Không gian ở ấm áp, cân bằng thẩm mỹ nội thất.'],
            ['en' => 'Coffee Shop', 'vi' => 'Quán cà phê', 'icon' => '☕', 'desc' => 'Trải nghiệm xúc giác, chịu ẩm và biểu cảm vật liệu.'],
            ['en' => 'Retail', 'vi' => 'Cửa hàng bán lẻ', 'icon' => '🛍️', 'desc' => 'Dẫn dắt hành vi mua sắm, bền và tạo điểm nhấn.'],
            ['en' => 'Apartment', 'vi' => 'Căn hộ chung cư', 'icon' => '🏬', 'desc' => 'Tinh gọn, bền vững, cân bằng kỹ thuật và ở.'],
        ];

        $surfaces = [
            ['en' => 'Floor', 'vi' => 'Bề mặt sàn', 'viShort' => 'Bề mặt sàn', 'enLabel' => 'Floor'],
            ['en' => 'Wall', 'vi' => 'Bề mặt tường', 'viShort' => 'Bề mặt tường', 'enLabel' => 'Wall'],
            ['en' => 'Ceiling', 'vi' => 'Hệ trần', 'viShort' => 'Hệ trần', 'enLabel' => 'Ceiling'],
            ['en' => 'Loose Furniture', 'vi' => 'Đồ rời', 'viShort' => 'Đồ rời', 'enLabel' => 'Loose'],
            ['en' => 'Detail Surface', 'vi' => 'Chi tiết kỹ thuật', 'viShort' => 'Chi tiết kỹ thuật', 'enLabel' => 'Detail'],
        ];

        $areaMatrix = [
            'Office' => [
                'Floor' => ['Reception', 'Open Work', 'Meeting Room', 'Executive', 'Corridor', 'Pantry', 'WC'],
                'Wall' => ['Reception', 'Open Work', 'Meeting Room', 'Corridor', 'Pantry', 'Executive'],
                'Ceiling' => ['Reception', 'Open Work', 'Meeting Room', 'Executive', 'Corridor'],
                'Loose Furniture' => ['Reception', 'Open Work', 'Meeting Room', 'Pantry', 'Lounge'],
                'Detail Surface' => ['Reception Desk', 'Pantry Counter', 'Meeting Table', 'Executive Desk', 'Corridor Handrail'],
            ],
            'School' => [
                'Floor' => ['Classroom', 'Corridor', 'Library', 'Canteen', 'Playground', 'WC', 'Admin'],
                'Wall' => ['Classroom', 'Corridor', 'Library', 'Canteen', 'Auditorium'],
                'Ceiling' => ['Classroom', 'Library', 'Auditorium', 'Corridor', 'Canteen'],
                'Loose Furniture' => ['Classroom', 'Library', 'Canteen', 'Admin', 'Auditorium'],
                'Detail Surface' => ['Lab Bench', 'Library Shelf', 'Canteen Counter', 'Classroom Desk'],
            ],
            'Townhouse' => [
                'Floor' => ['Living Room', 'Kitchen', 'Bedroom', 'WC', 'Stair', 'Terrace'],
                'Wall' => ['Living Room', 'Kitchen', 'Bedroom', 'Facade', 'WC', 'Stair'],
                'Ceiling' => ['Living Room', 'Kitchen', 'Bedroom', 'WC', 'Terrace'],
                'Loose Furniture' => ['Living Room', 'Bedroom', 'Kitchen', 'Terrace'],
                'Detail Surface' => ['Kitchen Counter', 'Stair Handrail', 'Wardrobe Surface', 'Bath Vanity'],
            ],
            'Coffee Shop' => [
                'Floor' => ['Serving Area', 'Seating Area', 'Bar Counter', 'WC', 'Terrace', 'Kitchen'],
                'Wall' => ['Bar Counter', 'Seating Area', 'Facade', 'WC', 'Kitchen', 'Serving Area'],
                'Ceiling' => ['Seating Area', 'Bar Counter', 'Serving Area', 'WC'],
                'Loose Furniture' => ['Seating Area', 'Bar Counter', 'Terrace', 'Serving Area'],
                'Detail Surface' => ['Bar Counter', 'Table Top', 'Facade Cladding', 'Menu Board'],
            ],
            'Retail' => [
                'Floor' => ['Sales Floor', 'Fitting Room', 'Cashier', 'Storage', 'WC', 'Entrance'],
                'Wall' => ['Sales Floor', 'Fitting Room', 'Facade', 'Cashier', 'Entrance'],
                'Ceiling' => ['Sales Floor', 'Fitting Room', 'Entrance', 'Cashier'],
                'Loose Furniture' => ['Sales Floor', 'Fitting Room', 'Cashier', 'Window Display'],
                'Detail Surface' => ['Cashier Counter', 'Display Shelf', 'Facade', 'Fitting Bench'],
            ],
            'Apartment' => [
                'Floor' => ['Living Room', 'Kitchen', 'Bedroom', 'Balcony', 'WC', 'Corridor'],
                'Wall' => ['Living Room', 'Kitchen', 'Bedroom', 'Balcony', 'WC', 'Corridor'],
                'Ceiling' => ['Living Room', 'Kitchen', 'Bedroom', 'Balcony', 'WC'],
                'Loose Furniture' => ['Living Room', 'Bedroom', 'Balcony', 'Kitchen', 'Corridor'],
                'Detail Surface' => ['Kitchen Counter', 'Wardrobe', 'Bath Vanity', 'Balcony Railing'],
            ],
        ];

        $areaLabels = [
            'Reception' => 'Khu lễ tân', 'Open Work' => 'Khu làm việc mở', 'Meeting Room' => 'Phòng họp',
            'Executive' => 'Phòng điều hành', 'Corridor' => 'Hành lang', 'Pantry' => 'Pantry',
            'WC' => 'Khu vệ sinh', 'Classroom' => 'Phòng học', 'Library' => 'Thư viện',
            'Canteen' => 'Căn tin', 'Playground' => 'Sân chơi', 'Admin' => 'Hành chính',
            'Auditorium' => 'Hội trường', 'Living Room' => 'Phòng khách', 'Kitchen' => 'Bếp',
            'Bedroom' => 'Phòng ngủ', 'Stair' => 'Cầu thang', 'Terrace' => 'Sân thượng',
            'Facade' => 'Mặt dựng', 'Serving Area' => 'Khu phục vụ', 'Seating Area' => 'Khu chỗ ngồi',
            'Bar Counter' => 'Quầy bar', 'Sales Floor' => 'Sàn bán hàng', 'Fitting Room' => 'Phòng thử đồ',
            'Cashier' => 'Thu ngân', 'Storage' => 'Kho', 'Entrance' => 'Lối vào',
            'Window Display' => 'Tủ trưng bày', 'Reception Desk' => 'Quầy lễ tân', 'Pantry Counter' => 'Bàn pantry',
            'Meeting Table' => 'Bàn họp', 'Executive Desk' => 'Bàn điều hành', 'Corridor Handrail' => 'Tay vịn',
            'Lab Bench' => 'Bàn thí nghiệm', 'Library Shelf' => 'Kệ thư viện', 'Canteen Counter' => 'Quầy căn tin',
            'Classroom Desk' => 'Bàn học', 'Kitchen Counter' => 'Mặt bếp', 'Stair Handrail' => 'Tay vịn cầu thang',
            'Wardrobe Surface' => 'Bề mặt tủ áo', 'Bath Vanity' => 'Bàn lavabo', 'Table Top' => 'Mặt bàn',
            'Facade Cladding' => 'Ốp mặt dựng', 'Menu Board' => 'Bảng thực đơn', 'Cashier Counter' => 'Quầy thu ngân',
            'Display Shelf' => 'Kệ trưng bày', 'Fitting Bench' => 'Băng thử đồ', 'Wardrobe' => 'Tủ áo',
            'Balcony' => 'Ban công', 'Balcony Railing' => 'Lan can',
        ];

        $configs = [
            'material_lab_typologies' => $typologies,
            'material_lab_surfaces' => $surfaces,
            'material_lab_area_matrix' => $areaMatrix,
            'material_lab_area_labels' => $areaLabels,
            'material_lab_title' => 'Thư viện vật liệu',
            'material_lab_subtitle' => 'Material Lab • V1.0',
            'material_lab_step1_title' => 'Công trình của bạn là gì?',
            'material_lab_step1_desc' => 'Chọn loại hình để gợi ý vật liệu phù hợp.',
            'material_lab_step2_title' => 'Vị trí nào cần vật liệu?',
            'material_lab_step2_desc' => 'Chọn bề mặt để lọc vật liệu phù hợp.',
            'material_lab_step3_title' => 'Khu vực chức năng nào?',
            'material_lab_step4_title' => 'Nhu cầu thực tế?',
            'material_lab_step4_desc' => 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.',
            'survey_mail_subject' => 'Thư mời đánh giá chất lượng dịch vụ & công trình - Workspace',
            'survey_mail_template' => "Kính gửi Quý Khách hàng,\n\nNhằm không ngừng nâng cao chất lượng thi công và dịch vụ bảo hành, Workspace trân trọng kính mời Quý Khách hàng dành ít phút đánh giá sự hài lòng về dự án vừa qua.\n\nÝ kiến đóng góp quý báu của Quý vị là kim chỉ nam giúp chúng tôi hoàn thiện hơn mỗi ngày.\n\nTrân trọng cảm ơn!",
        ];

        foreach ($configs as $k => $v) {
            DB::table('settings')->updateOrInsert(
                ['key' => $k],
                [
                    'payload' => json_encode(is_array($v) ? $v : ['value' => $v]),
                    'value' => is_scalar($v) ? (string) $v : null,
                    'group' => 'general',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
