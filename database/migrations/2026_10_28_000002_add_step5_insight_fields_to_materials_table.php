<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                if (! Schema::hasColumn('materials', 'ai_standards')) {
                    $table->text('ai_standards')->nullable()->after('tech_description');
                }
                if (! Schema::hasColumn('materials', 'fire_requirement')) {
                    $table->string('fire_requirement')->nullable()->after('ai_standards');
                }
                if (! Schema::hasColumn('materials', 'metrics')) {
                    $table->string('metrics')->nullable()->after('fire_requirement');
                }
                if (! Schema::hasColumn('materials', 'ws_experience')) {
                    $table->text('ws_experience')->nullable()->after('experience_description');
                }
                if (! Schema::hasColumn('materials', 'common_errors')) {
                    $table->string('common_errors')->nullable()->after('ws_experience');
                }
                if (! Schema::hasColumn('materials', 'tips')) {
                    $table->string('tips')->nullable()->after('common_errors');
                }
            });

            // Backfill tailored per-material standards, fire requirements, metrics, experience, errors, tips
            $perMaterialInsights = [
                'lvt' => [
                    'ai_standards' => "• TCVN 8264:2009 & EN 14041 – Sàn nhựa LVT Class 33 AC5 chịu tải trọng văn phòng 5kN/m².\n• WELL v2 Feature S03 & S04 – Độ chống trượt R10, tiêu âm va chạm ΔLw = 18dB.\n• QCVN 06:2022/BXD – Cấp chống cháy lan Bfl-s1 cho sàn thương mại.\n• LEED v4.1 EQ Low-Emitting – Đạt chứng nhận FloorScore, TVOC < 0.5 mg/m³.",
                    'fire_requirement' => 'Bfl-s1 (Chậm cháy lan bề mặt sàn)',
                    'metrics' => 'R10 • Class 33 AC5 • ΔLw 18dB • E1/FloorScore',
                    'ws_experience' => "• Đã thi công 23 phòng họp & khu làm việc mở dùng Moduleo 55 – đi êm chân, tiêu âm bước chân 18dB rất rõ.\n• Bài học: 2 công trình tầng trệt độ ẩm nền >78% bị phồng mép sau 14 tháng.\n• Thẩm mỹ: Vân gỗ sồi mờ tự nhiên, lên đèn 4000K ấm áp, không bị bóng lóa.",
                    'common_errors' => 'Nền ẩm >75% phồng mép • Cán nền lồi lõm >3mm • Dùng keo gốc nước giá rẻ',
                    'tips' => 'Đo ẩm nền <75% • Cán vữa tự san phẳng <2mm/2m • Dùng keo Wakol D3310 • Chừa khe 5mm',
                ],
                'terrazzo' => [
                    'ai_standards' => "• QCVN 16:2023/BXD & TCVN 7742 – Đá mài Terrazzo đúc sẵn cường độ nén ≥ 40MPa, độ mài mòn sâu < 29mm³.\n• QCVN 06:2022/BXD – Vật liệu không cháy (Class A1), chống trượt R11 an toàn cho sảnh & hành lang thoát nạn.\n• WELL v2 X06 & LEED v4.1 – Vật liệu vô cơ không phát thải VOC (0 g/L), cốt liệu đá tái chế.",
                    'fire_requirement' => 'Class A1 – Không cháy (An toàn tuyệt đối lối thoát nạn)',
                    'metrics' => 'R11 • Nén 40MPa • Hấp thụ nước <3% • VOC 0g/L',
                    'ws_experience' => "• Đã lát 12 sảnh chính & quán cà phê với Terrazzo Precast 60×60 – siêu bền 15 năm, kéo xe pallet không xước.\n• Ưu điểm lớn: Sau 5–7 năm sử dụng có thể mài phủ bóng/mờ lại như mới mà không cần đục bỏ.\n• Lưu ý trọng lượng viên gạch nặng (~32kg/m²), cần đầm kỹ lớp vữa lót.",
                    'common_errors' => 'Không phủ sealer chống thấm làm ố trà/cà phê • Mạch gạch lệch do viên nặng',
                    'tips' => 'Phủ 2 lớp chống thấm Lithofin • Dùng ke cân bằng 2mm • Chà ron epoxy cùng tông màu',
                ],
                'porcelain' => [
                    'ai_standards' => "• QCVN 16:2023/BXD & TCVN 7745 – Gạch Porcelain nhóm BIa độ hút nước E ≤ 0.5%, độ bền uốn ≥ 35 N/mm².\n• QCVN 06:2022/BXD – Vật liệu không cháy A1, bề mặt R11 chống trượt cho hành lang, WC, lối thoát hiểm.\n• LEED v4.1 – Gạch nung nhiệt độ cao hoàn toàn không chứa VOC và formaldehyde.",
                    'fire_requirement' => 'Class A1 – Không cháy (Đạt chuẩn sảnh & thoát nạn)',
                    'metrics' => 'R11 • PEI 4 • Hút nước <0.5% • Không cháy A1',
                    'ws_experience' => "• Workspace đã triển khai 41 văn phòng & trường học với Taicera GP-03 – sau 8 năm không sứt mẻ, không bay màu.\n• Tồn kho luôn sẵn >4.000m², giá 275k/m² tối ưu chi phí nhất trong nhóm sàn bền 15 năm.\n• Chịu được nền tầng 1 độ ẩm >80% mà không sợ phồng rộp như sàn gỗ hay vinyl.",
                    'common_errors' => 'Lát bằng hồ dầu truyền thống dễ bộp gạch khổ lớn • Ron xi măng trắng bị đen',
                    'tips' => 'Dán bằng keo Weber.tai Fix • Dùng ke vít cân bằng • Chà ron chống bám bẩn 2mm',
                ],
                'laminate-hpl' => [
                    'ai_standards' => "• EN 438-3 & ISO 4586 – Tấm Laminate HPL chịu va đập, chống trầy xước và chịu nhiệt khô 180°C.\n• QCVN 06:2022/BXD – Đạt cấp chậm cháy B1 (B-s1, d0) cho tấm ốp tường hành lang và vách nội thất.\n• WELL v2 X06 & ISO 22196 – Kháng khuẩn bề mặt 99%, phát thải formaldehyde đạt chuẩn E0 (<0.05 mg/m³).",
                    'fire_requirement' => 'Fire B1 / B-s1, d0 (Chậm cháy, ít khói cho vách & hành lang)',
                    'metrics' => 'Fire B1 • E0 (<0.05mg/m³) • Dày 0.8mm • Kháng khuẩn ISO 22196',
                    'ws_experience' => "• Đã thi công 22 hệ tủ bếp pantry, vách ốp hành lang & vách ngăn WC bằng HPL Formica.\n• Bề mặt mờ mịn chống bám vân tay rất tốt, lau sạch vết bút bi và cà phê dễ dàng.\n• Cạnh dán máy PUR không lộ đường keo và không bị ngấm nước trương nở cốt gỗ.",
                    'common_errors' => 'Dán tay bằng keo con chó bị phồng rộp mặt • Cốt MDF thường bị ẩm trương cạnh',
                    'tips' => 'Ép máy nguội/nóng trên cốt MDF chống ẩm HMR • Dán cạnh bằng keo PUR chống nước',
                ],
                'kvadrat' => [
                    'ai_standards' => "• EN 1021-1/2 & BS 5852 – Vải nỉ len 100% tự nhiên kháng cháy tàn thuốc và nguồn lửa diêm tiêu chuẩn Châu Âu.\n• ISO 12947 – Độ bền mài mòn Martindale đạt 45.000 vòng, phù hợp ghế sofa lounge và ghế văn phòng.\n• EU Ecolabel & Greenguard Gold – Không chứa kim loại nặng, thuốc nhuộm an toàn, tiêu âm tần số trung – cao.",
                    'fire_requirement' => 'EN 1021-1&2 / BS 5852 (Kháng cháy nội thất bọc nệm)',
                    'metrics' => 'Wool 100% • 45.000 Martindale • NRC ~0.35 • Greenguard Gold',
                    'ws_experience' => "• Đã bọc 11 khu vực sofa lounge điều hành & booth thảo luận bằng Kvadrat Divina Melange.\n• Xúc giác ấm, màu melange chiều sâu rất sang trọng, hỗ trợ hút âm hội thoại trong không gian mở.\n• Giá thành cao (890k/m) và thời gian nhập Đan Mạch 6–8 tuần nên cần chốt mã màu sớm.",
                    'common_errors' => 'Giặt ướt bằng hóa chất mạnh làm co rút sợi len • Đặt sát khu vực ăn uống dễ dính dầu mỡ',
                    'tips' => 'Phủ nano chống thấm nhẹ trước khi bàn giao • Vệ sinh hút bụi định kỳ & giặt khô chuyên dụng',
                ],
                'dulux' => [
                    'ai_standards' => "• QCVN 16:2023/BXD & TCVN 8652:2020 – Sơn nước nội thất cao cấp, độ bền chùi rửa > 5.000 chu kỳ.\n• LEED v4.1 EQ & WELL v2 A06 – Hàm lượng chất hữu cơ bay hơi cực thấp VOC < 10g/L (tiêu chuẩn cho phép < 50g/L).\n• Chứng nhận Singapore Green Label – Không thêm chì và thủy ngân, an toàn cho trường học & văn phòng.",
                    'fire_requirement' => 'Không bắt lửa trên nền tường xây / thạch cao chống cháy',
                    'metrics' => 'VOC <10g/L • Lau chùi >5000 lần • Độ phủ 12m²/L • Green Label',
                    'ws_experience' => "• Hơn 100+ công trình văn phòng, nhà phố, căn hộ dùng Dulux Ambiance 5-in-1 – pha màu tại đại lý chỉ 30 phút.\n• Bề mặt mờ siêu mịn giúp che khuyết điểm tường tốt hơn sơn bóng, dặm vá sau bảo hành không bị lộ vết.\n• Thi công nhanh, gần như không mùi, nhân viên có thể vào làm việc ngay sau 24 giờ.",
                    'common_errors' => 'Sơn khi tường còn ẩm >16% gây phồng rộp • Bỏ qua lớp sơn lót kháng kiềm làm loang màu',
                    'tips' => 'Đo ẩm tường <16% • Bả 2 lớp + 1 lót kháng kiềm + 2 phủ • Dùng rulo lông ngắn 12mm cho mặt mịn',
                ],
                'armstrong' => [
                    'ai_standards' => "• ASTM E84 & EN 13501-1 – Tấm trần sợi khoáng đạt chuẩn chống cháy lan Class A (Euroclass A2-s1, d0).\n• ASTM C423 & TCVN 4474 – Hệ số tiêu âm NRC = 0.70 và cách âm truyền qua CAC = 35dB (WELL v2 Feature S04).\n• TCVN 4088 – Khả năng chịu độ ẩm RH99% ở 49°C không провисание (không võng tấm) tại khí hậu Việt Nam.",
                    'fire_requirement' => 'Class A / Euroclass A2-s1,d0 (Chống cháy lan hệ trần)',
                    'metrics' => 'NRC 0.70 • CAC 35dB • Chống ẩm RH99% • Phản xạ sáng 87%',
                    'ws_experience' => "• Đã lắp đặt 27 dự án trần văn phòng & trường học với Armstrong Ultima+ – giảm tiếng vang phòng họp rõ rệt.\n• Chịu ẩm RH99% nên qua mùa nồm miền Bắc hoặc ngưng tụ điều hòa không bị võng hay mốc ố vàng.\n• Hệ khung xương nổi T-24/T-15 tháo tấm kiểm tra cơ điện MEP trên trần rất tiện lợi.",
                    'common_errors' => 'Thợ tay bẩn chạm vào tấm khi lắp để lại vết đen • Khoảng cách ty treo >1.2m làm võng xương',
                    'tips' => 'Đeo găng tay trắng khi lắp tấm • Treo ty khoảng cách ≤1.0m • Dùng xương chính đồng bộ',
                ],
                'glass-lowe' => [
                    'ai_standards' => "• QCVN 09:2017/BXD (Sử dụng năng lượng hiệu quả) – Hệ số truyền nhiệt U-value = 1.8 W/m²K, SHGC ≤ 0.42.\n• TCVN 7455:2013 – Kính cường lực an toàn 8mm/10mm chịu va đập gấp 4–5 lần kính nổi thông thường.\n• WELL v2 L01 Daylight – Độ truyền sáng tự nhiên VLT > 68%, giảm chói và cản 99% tia UV.",
                    'fire_requirement' => 'Kính an toàn cường lực không bắt lửa (TCVN 7455)',
                    'metrics' => 'U-value 1.8 • Cách nhiệt 68% • Cản 99% UV • Cách âm ~32dB',
                    'ws_experience' => "• Đã thi công 14 hệ vách kính phòng họp & mặt dựng Saint-Gobain Low-E – giảm tải điều hòa tới 30%.\n• Phôi kính Clear Low-Iron trong suốt, không bị ánh xanh rêu ở cạnh kính, nhìn không gian rất rộng.\n• Gia công закалка (tôi cường lực) tại nhà máy 5–7 ngày, lắp kèm gioăng từ tính kín âm.",
                    'common_errors' => 'Đặt ngược mặt phủ Low-E ra ngoài dễ xước • Đo sai kích thước trước khi tôi kính (không cắt lại được)',
                    'tips' => 'Khảo sát laser sau khi hoàn thiện sàn/trần • Chèn gioăng EPDM + keo trung tính Dow Corning 791',
                ],
                'corian' => [
                    'ai_standards' => "• NSF/ANSI 51 & ISO 22196 – Đá nhân tạo Solid Surface không lỗ rỗng (non-porous), kháng khuẩn tuyệt đối cho y tế & F&B.\n• EN 13501-1 – Đạt cấp chống cháy B-s1, d0 (chậm cháy, không nhỏ giọt lửa).\n• Greenguard Gold & LEED v4.1 – Hàm lượng VOC cực thấp, mối nối keo đồng màu liền khối không khe hở.",
                    'fire_requirement' => 'Euroclass B-s1, d0 (Chậm cháy, không sinh khói độc)',
                    'metrics' => 'NSF Kháng khuẩn • Liền khối 0mm mạch • Uốn cong nhiệt 160°C',
                    'ws_experience' => "• Đã chế tác 8 quầy lễ tân uốn cong 3D & mặt bàn đảo bếp cao cấp bằng DuPont Corian.\n• Ghép nối không thấy vết mối ghép, sau vài năm nếu trầy xước nhẹ chỉ cần đánh nhám mịn P400–P1000 là mới 100%.\n• Giá 1.1tr+/m² và thời gian đặt hàng 6–8 tuần, phù hợp hạng mục điểm nhấn Signature.",
                    'common_errors' => 'Đặt nồi vừa đun sôi trực tiếp không lót • Khoét góc vuông sắc ở chậu rửa dễ nứt nhiệt',
                    'tips' => 'Bo góc R ≥ 10mm tại vị trí khoét chậu • Gia cường khung đỡ cách khoảng ≤ 400mm • Dùng keo chính hãng',
                ],
                'veneer-oak' => [
                    'ai_standards' => "• FSC® Certified (Forest Stewardship Council) – Nguồn gỗ sồi tái sinh bền vững đạt điểm LEED v4.1 MR.\n• QCVN 16:2023/BXD & EN 717-1 – Cốt gỗ và keo ép đạt chuẩn E0 (Formaldehyde ≤ 0.05 ppm).\n• EN 13501-1 – Có thể xử lý phủ sơn chậm cháy chuẩn B-s2, d0 cho vách ốp không gian điều hành.",
                    'fire_requirement' => 'B-s2, d0 (Khi phủ sơn PU/UV chậm cháy chuyên dụng)',
                    'metrics' => 'Chuẩn FSC • Cốt E0 • Vân sọc thẳng Rift Cut • Độ ẩm gỗ 8–12%',
                    'ws_experience' => "• Đã hoàn thiện 19 phòng Chủ tịch / Giám đốc & phòng họp VIP với Veneer Oak White Rift Alpi.\n• Vân sọc thẳng đều tăm tắp, hoàn thiện dầu lau (Natural Oiled) giữ nguyên sớ gỗ chạm rất thật tay.\n• Hàng nhập Italy 6–8 tuần, đòi hỏi xưởng sơn PU/Oiled tay nghề cao.",
                    'common_errors' => 'Độ ẩm cốt gỗ >14% gây cong vênh cánh tủ • Phun sơn bóng làm mất vẻ tự nhiên của vân Rift',
                    'tips' => 'Kiểm ẩm cốt gỗ <12% • Hoàn thiện sơn mờ 5–10% hoặc dầu Osmo • Dùng bản lề giảm chấn tải nặng',
                ],
                'carpet' => [
                    'ai_standards' => "• QCVN 06:2022/BXD & EN 13501-1 – Thảm viên đạt chuẩn chống cháy lan Bfl-s1 (phù hợp văn phòng tầng cao).\n• TCVN 4474 & ISO 354 – Hệ số tiêu âm NRC = 0.28, giảm tiếng ồn va chạm bước chân tới 28dB (WELL S04).\n• Carbon Neutral Floors™ & Green Label Plus – Đế CQuest™Bio không chứa bitumen và PVC, trung hòa carbon.",
                    'fire_requirement' => 'Bfl-s1 (Chống cháy lan tiêu chuẩn thảm văn phòng cao tầng)',
                    'metrics' => 'Tiêu âm 28dB • NRC 0.28 • Sợi Nylon 6 tái chế • Carbon Neutral',
                    'ws_experience' => "• Đã trải 9 khu Open Work Zone quy mô 500–2.000m² với thảm Interface Composure – giảm 40% tiếng ồn văn phòng.\n• Thi công bằng miếng dán TacTiles không dùng keo lỏng nên không có mùi keo, thay thế từng tấm bẩn trong 2 phút.\n• Luôn sẵn kho, thi công cực nhanh (300m²/ngày).",
                    'common_errors' => 'Lát thảm vào khu vực Pantry/cà phê dễ đổ nước ố bẩn • Dùng keo cố định khó tháo lắp điện sàn',
                    'tips' => 'Dùng tem dán TacTiles thay keo lỏng • Phối LVT cho khu Pantry • Dự trữ dư 3–5% để thay tấm bẩn',
                ],
                'pet-felt' => [
                    'ai_standards' => "• ASTM C423 & ISO 354 – Hệ số hấp thụ âm NRC = 0.45 (dán sát tường) đến 0.75 (có khoảng hở khí 50mm).\n• QCVN 06:2022/BXD & ASTM E84 – Đạt cấp chống cháy lan Class B / Euroclass B-s1, d0 cho tấm ốp tiêu âm.\n• LEED v4.1 MR – Sản xuất từ ≥ 60% sợi nhựa PET tái chế từ chai nhựa, không phát thải bụi sợi ngứa.",
                    'fire_requirement' => 'ASTM E84 Class B / Euroclass B-s1, d0 (Chậm cháy, không khói độc)',
                    'metrics' => 'NRC 0.45–0.75 • 60% PET tái chế • Dày 9mm • VOC 0g/L',
                    'ws_experience' => "• Đã ốp tiêu âm 18 phòng họp & phonebooth bằng Feltouch 9mm – giảm thời gian vang RT60 từ 0.65s xuống 0.38s.\n• Tấm khổ 1.2×2.4m cắt CNC tạo rãnh V-cut hoặc khắc logo thương hiệu rất sắc nét, vừa tiêu âm vừa trang trí.\n• Không gây ngứa bụi như bông khoáng truyền thống, ghim giấy note trực tiếp lên vách được.",
                    'common_errors' => 'Cắt bằng dao cùn làm xơ mép nỉ • Bơm keoシリコン (silicone) quá dày làm lồi bề mặt tấm',
                    'tips' => 'Cắt bằng máy CNC lưỡi dao mới • Dán keo Tibond chấm điểm kết hợp súng bắn đinh chỉ không đầu',
                ],
                'inox' => [
                    'ai_standards' => "• JIS G4305 / ASTM A240 – Thép không gỉ Austenitic SUS 304 (18% Cr, 8% Ni) chống ăn mòn và oxy hóa cao.\n• QCVN 06:2022/BXD – Vật liệu kim loại không cháy hoàn toàn (Euroclass A1), chịu nhiệt độ > 800°C.\n• WELL v2 & HACCP – Bề mặt trơ, không phát thải VOC, dễ khử trùng cho tay vịn, quầy bar và ốp cột.",
                    'fire_requirement' => 'Class A1 – Không cháy (Chịu nhiệt >800°C)',
                    'metrics' => 'SUS 304 chuẩn • Hairline sọc nhuyễn • Không cháy A1 • Tái chế 100%',
                    'ws_experience' => "• Đã gia công 34 hạng mục tay vịn cầu thang, len chân tường âm và quầy bar cà phê bằng Inox Hairline 304 Posco.\n• Bề mặt xước mờ Hairline kết hợp phủ chống vân tay (Anti-fingerprint) giữ bề mặt luôn sạch, không bóng nhoáng.\n• Gia công chấn gấp CNC V-grooving tại Việt Nam cho góc vuông sắc cạnh như khối đặc.",
                    'common_errors' => 'Dùng nhầm Inox 201 bị ố gỉ sét sau 6 tháng • Không soi rãnh V-cut làm góc chấn bị bo tròn thô',
                    'tips' => 'Kiểm tra thuốc thử chuẩn Inox 304 • Soi rãnh V-grooving trước khi chấn • Hàn TIG mài xử lý sọc đồng bộ',
                ],
                'rubber-nora' => [
                    'ai_standards' => "• QCVN 06:2022/BXD & EN 13501-1 – Sàn cao su tự nhiên đạt chuẩn chống cháy Cfl-s1 / Bfl-s1, khói không chứa halogen độc.\n• DIN 51130 & TCVN ISO 22196 – Chống trượt R9/R10, bề mặt đặc khít kháng khuẩn tự nhiên không cần phủ wax.\n• WELL v2 S03 Movement – Độ đàn hồi giảm chấn 14%, giảm mỏi cơ khớp và chấn thương khi ngã cho trường học / y tế.",
                    'fire_requirement' => 'Bfl-s1 / Cfl-s1 (Không chứa PVC & Halogen, khói không độc)',
                    'metrics' => 'R9/R10 • Giảm chấn 14% • Tiêu âm 10dB • Không cần phủ bóng',
                    'ws_experience' => "• Đã thi công 7 trường học quốc tế & phòng khám với Nora Noraplan (Đức) – trẻ chạy nhảy ngã không đau, cực kỳ êm.\n• Không cần phủ wax bảo dưỡng định kỳ như sàn Vinyl cuộn thông thường, tiết kiệm chi phí vận hành 10 năm.\n• Chống cháy tàn thuốc lá và hóa chất phòng thí nghiệm rất tốt.",
                    'common_errors' => 'Nền tự san phẳng chưa khô kỹ đã trải keo • Không lu lăn kỹ bằng con lăn 50kg làm phồng khí',
                    'tips' => 'Cán vữa tự san phẳng Mapei/Ardex • Dùng keo chuyên dụng Nora • Lu lăn ép 2 chiều bằng con lăn 50kg',
                ],
                'gypsum' => [
                    'ai_standards' => "• QCVN 06:2022/BXD & EN 520 – Tấm thạch cao tiêu âm đục lỗ đạt chuẩn không cháy / chống cháy lan Class A2-s1, d0.\n• ISO 354 & WELL v2 S04 – Hệ số tiêu âm NRC = 0.65 (kết hợp lớp giấy tiêu âm mặt sau và bông khoáng).\n• Công nghệ Cleaneo® Zeolite – Tự động hấp thụ và phân hủy khí Formaldehyde & mùi hôi trong không khí.",
                    'fire_requirement' => 'Euroclass A2-s1, d0 (Không cháy lan, an toàn trần thoát hiểm)',
                    'metrics' => 'NRC 0.65 • Khử Formaldehyde (Zeolite) • Trần liền mạch không lộ xương',
                    'ws_experience' => "• Đã thi công 35 trần phòng học, hội trường & sảnh văn phòng với Knauf Cleaneo – giải pháp trần tiêu âm phẳng đẹp nhất.\n• Bề mặt đục lỗ liền mạch (không lộ khung xương như trần thả), sơn nước hoàn thiện đồng bộ với trần thường.\n• Chi phí chỉ 180k/m² vật tư, thi công nhanh 2 ngày/100m².",
                    'common_errors' => 'Dùng rulo nhiều sơn làm bít các lỗ tiêu âm • Bả matit tràn vào lỗ đục tại vị trí mối nối tấm',
                    'tips' => 'Dùng dụng cụ bơm keo mối nối chuyên dụng Knauf Uniflott • Lăn sơn bằng rulo lông ngắn tránh bít lỗ',
                ],
            ];

            foreach ($perMaterialInsights as $code => $data) {
                DB::table('materials')->where('code', $code)->update($data);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('materials')) {
            Schema::table('materials', function (Blueprint $table) {
                $cols = [];
                foreach (['ai_standards', 'fire_requirement', 'metrics', 'ws_experience', 'common_errors', 'tips'] as $col) {
                    if (Schema::hasColumn('materials', $col)) {
                        $cols[] = $col;
                    }
                }
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
