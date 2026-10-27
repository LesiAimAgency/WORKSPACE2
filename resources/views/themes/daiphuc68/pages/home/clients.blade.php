{{-- Section 7: Clients Showcase (Khách Hàng - 100% Khớp Thiết Kế Khách hàng.svg) --}}
@php
  $settings = $settings ?? [];
  $headTop = $settings['headline_top'] ?? 'UY TÍN';
  $headMid = $settings['headline_middle'] ?? 'TẠO NÊN';
  $headBot = $settings['headline_bottom'] ?? 'THƯƠNG HIỆU';
  $subText = $settings['sub_text'] ?? 'Lắng nghe phản hồi từ những khách hàng chúng tôi đã đồng hành';
  $btnText = $settings['btn_text'] ?? 'NHẬN TƯ VẤN NGAY';

  // 13 authentic items matching Figma Khách hàng.svg (with extracted high-res assets)
  $defaultList = [
    [
      'client_id' => 'client-1',
      'image' => asset('themes/daiphuc68/assets/images/clients/image0_158_5464.jpg'),
      'name' => 'Chị TRẦN THỊ MAI',
      'role' => 'Giám đốc Chuỗi Cửa Hàng Organic Green',
      'quote' => 'Hệ thống điện chiếu sáng và thiết bị trung thế Đại Phúc 68 cung cấp hoạt động cực kỳ ổn định, tiết kiệm đáng kể chi phí vận hành.',
    ],
    [
      'client_id' => 'client-2',
      'image' => asset('themes/daiphuc68/assets/images/clients/image9_158_5464.png'),
      'name' => 'Chị TRỊNH THU TRANG',
      'role' => 'Tổng Giám đốc Công ty CP Thời trang Việt Hàn',
      'quote' => 'Hệ thống thiết bị điện trung thế và chiếu sáng của Đại Phúc 68 đạt chuẩn cao, đảm bảo độ an toàn tuyệt đối cho toàn bộ cơ sở.',
    ],
    [
      'client_id' => 'client-3',
      'image' => asset('themes/daiphuc68/assets/images/clients/image2_158_5464.jpg'),
      'name' => 'Chị HOÀNG KIM NGÂN',
      'role' => 'Phó Giám đốc Vận hành Khách sạn SunRise',
      'quote' => 'Tiến độ thi công rất chuẩn xác, giải pháp tư vấn kỹ thuật tận tâm và xử lý nhanh gọn các vấn đề phát sinh tại công trình.',
    ],
    [
      'client_id' => 'client-4',
      'image' => asset('themes/daiphuc68/assets/images/clients/image11_158_5464.jpg'),
      'name' => 'Anh LÊ VĂN HÙNG',
      'role' => 'Giám đốc Nhà máy Chế biến Thực phẩm Việt Đức',
      'quote' => 'Hệ thống vận hành trơn tru suốt hơn 3 năm qua. Dịch vụ hậu mãi và bảo trì định kỳ của Đại Phúc rất đáng tin cậy.',
    ],
    [
      'client_id' => 'client-5',
      'image' => asset('themes/daiphuc68/assets/images/clients/image4_158_5464.jpg'),
      'name' => 'Chị NGUYỄN PHƯƠNG ANH',
      'role' => 'Trưởng phòng Quản lý Xây dựng Bất động sản An Gia',
      'quote' => 'Quy trình làm việc bài bản, giám sát kỹ lưỡng từng hạng mục lắp đặt. Chúng tôi đánh giá rất cao tinh thần trách nhiệm của đội ngũ.',
    ],
    [
      'client_id' => 'client-6',
      'image' => asset('themes/daiphuc68/assets/images/clients/image6_158_5464.jpg'),
      'name' => 'Chị LÊ BÍCH PHƯỢNG',
      'role' => 'Chủ chuỗi Showroom Thời trang Milano',
      'quote' => 'Hệ thống ánh sáng chiếu điểm tôn vinh sản phẩm rất xuất sắc, tiến độ bàn giao trước thời hạn dự kiến.',
    ],
    [
      'client_id' => 'client-7',
      'image' => asset('themes/daiphuc68/assets/images/clients/image8_158_5464.jpg'),
      'name' => 'Anh PHẠM QUỐC THÁI',
      'role' => 'Chủ đầu tư Chuỗi Cafe & Homestay Mộc Châu',
      'quote' => 'Giải pháp thiết kế và lắp đặt điện thẩm mỹ, an toàn tuyệt đối. Khách hàng đến quán đều ấn tượng với không gian ánh sáng.',
    ],
    [
      'client_id' => 'client-8',
      'image' => asset('themes/daiphuc68/assets/images/clients/image7_158_5464.jpg'),
      'name' => 'Anh VŨ MINH TRÍ',
      'role' => 'Giám đốc Kỹ thuật Khu Nghỉ Dưỡng Ocean Park',
      'quote' => 'Đội ngũ kỹ sư giàu kinh nghiệm, chuyên môn sâu và hỗ trợ kỹ thuật bảo hành 24/7 cực kỳ chu đáo và nhiệt tình.',
    ],
    [
      'client_id' => 'client-9',
      'image' => asset('themes/daiphuc68/assets/images/clients/image5_158_5464.jpg'),
      'name' => 'Chị ĐỖ BÍCH NGỌC',
      'role' => 'Giám đốc Điều hành Chuỗi Trung Tâm Anh Ngữ V-Smart',
      'quote' => 'Đại Phúc 68 không chỉ đáp ứng yêu cầu kỹ thuật khắt khe mà còn tối ưu hóa chi phí đầu tư rất hợp lý cho doanh nghiệp chúng tôi.',
    ],
    [
      'client_id' => 'client-10',
      'image' => asset('themes/daiphuc68/assets/images/clients/image1_158_5464.jpg'),
      'name' => 'Anh NGUYỄN VĂN A',
      'role' => 'Chủ nhà hàng THE LONG',
      'quote' => 'Đại Phúc đã mang đến cho tôi một gian hàng đẹp, đầy đủ công năng và đúng tiến độ. Đội ngũ rất chuyên nghiệp và tận tâm.',
    ],
    [
      'client_id' => 'client-11',
      'image' => asset('themes/daiphuc68/assets/images/clients/image10_158_5464.jpg'),
      'name' => 'Anh TRẦN QUANG HUY',
      'role' => 'Kiến trúc sư Trưởng Công ty TK V-Arch',
      'quote' => 'Sự kết hợp giữa giải pháp kiến trúc và bố trí điện năng lượng sạch của Đại Phúc 68 mang lại giá trị bền vững vượt trội.',
    ],
    [
      'client_id' => 'client-12',
      'image' => asset('themes/daiphuc68/assets/images/clients/image3_158_5464.jpg'),
      'name' => 'Chị LÊ THU HÀ',
      'role' => 'Quản lý Dự án Tòa nhà Sunrise Tower',
      'quote' => 'Chất lượng vật tư thiết bị điện chuẩn chính hãng, chứng từ CO/CQ đầy đủ rõ ràng. Tôi hoàn toàn yên tâm khi đồng hành cùng Đại Phúc 68.',
    ],
    [
      'client_id' => 'client-13',
      'image' => asset('themes/daiphuc68/assets/images/clients/image12_158_5464.jpg'),
      'name' => 'Chị NGUYỄN THỊ THU HƯƠNG',
      'role' => 'Chủ hệ thống Spa & Chăm sóc Sức khỏe Hương Sen',
      'quote' => 'Đại Phúc 68 luôn lắng nghe và đưa ra giải pháp phù hợp nhất với ngân sách và công năng mà chúng tôi mong muốn.',
    ],
  ];

  $rawTestimonials = (!empty($settings['testimonials']) && is_array($settings['testimonials'])) 
    ? array_values($settings['testimonials']) 
    : $defaultList;

  // Normalize list with fallback to defaultList
  $testimonials = [];
  for ($i = 0; $i < 13; $i++) {
    $fallback = $defaultList[$i] ?? $defaultList[0];
    $raw = $rawTestimonials[$i] ?? $fallback;
    $cId = !empty($raw['client_id']) ? $raw['client_id'] : ('client-' . ($i + 1));
    $imgPath = !empty($raw['image'])
      ? (str_starts_with($raw['image'], 'http') ? $raw['image'] : asset(ltrim($raw['image'], '/')))
      : $fallback['image'];

    $testimonials[$i] = [
      'id' => $cId,
      'name' => $raw['name'] ?? $fallback['name'],
      'role' => $raw['role'] ?? $fallback['role'],
      'quote' => $raw['quote'] ?? $fallback['quote'],
      'image' => $imgPath,
    ];
  }

  // 9 Columns Architecture (Không dùng position: absolute, chia thành 9 cột với margin dọc chuẩn)
  $columns = [
    // Cột 1: Ghost (-35px), Client 0 (120px), Client 1 (298px)
    1 => [
      ['type' => 'ghost', 'margin_top' => -35],
      ['type' => 'client', 'idx' => 0, 'margin_top' => -22],
      ['type' => 'client', 'idx' => 1, 'margin_top' => 1],
    ],
    // Cột 2: Client 2 (55px), Client 3 (236px)
    2 => [
      ['type' => 'client', 'idx' => 2, 'margin_top' => 55],
      ['type' => 'client', 'idx' => 3, 'margin_top' => 4],
    ],
    // Cột 3: Ghost (5px), Client 4 (165px)
    3 => [
      ['type' => 'ghost', 'margin_top' => 5],
      ['type' => 'client', 'idx' => 4, 'margin_top' => -17],
    ],
    // Cột 4: Ghost (-50px), Client 5 (88px)
    4 => [
      ['type' => 'ghost', 'margin_top' => -50],
      ['type' => 'client', 'idx' => 5, 'margin_top' => -39],
    ],
    // Cột 5: Ghost (5px), Client 6 (138px)
    5 => [
      ['type' => 'ghost', 'margin_top' => 5],
      ['type' => 'client', 'idx' => 6, 'margin_top' => -44],
    ],
    // Cột 6: Client 7 (58px)
    6 => [
      ['type' => 'client', 'idx' => 7, 'margin_top' => 58],
    ],
    // Cột 7: Ghost (5px), Client 8 (165px)
    7 => [
      ['type' => 'ghost', 'margin_top' => 5],
      ['type' => 'client', 'idx' => 8, 'margin_top' => -17],
    ],
    // Cột 8: Ghost (-50px), Client 9 (88px), Client 10 (270px)
    8 => [
      ['type' => 'ghost', 'margin_top' => -50],
      ['type' => 'client', 'idx' => 9, 'margin_top' => -39],
      ['type' => 'client', 'idx' => 10, 'margin_top' => 5],
    ],
    // Cột 9: Ghost (5px), Client 11 (165px), Client 12 (348px)
    9 => [
      ['type' => 'ghost', 'margin_top' => 5],
      ['type' => 'client', 'idx' => 11, 'margin_top' => -17],
      ['type' => 'client', 'idx' => 12, 'margin_top' => 6],
    ],
  ];
@endphp

<section class="homepage-section relative w-full lg:h-[100svh] lg:min-h-[100svh] box-border overflow-hidden bg-[#F6F6F6] text-neutral-900 flex flex-col items-center justify-between select-none" id="home-clients" aria-label="Khách Hàng - Đại Phúc 68">

  {{-- Desktop 1440x900 Architectural Canvas Stage (>= 1024px, 100% Khớp Khách hàng.svg) --}}
  <div class="hidden lg:block w-full max-w-[1440px] h-[900px] relative self-center flex-1 overflow-hidden" id="clients-canvas-stage">
    
    {{-- 9 Staggered Columns Container --}}
    <div class="clients-grid-9 grid grid-cols-9 gap-2 w-full h-full relative z-10 box-border">
      @foreach($columns as $colNum => $items)
        <div class="client-stage-col client-col-{{ $colNum }} flex flex-col items-center w-full" style="gap:12px">
          @foreach($items as $itemIdx => $item)
            @if($item['type'] === 'ghost')
              <div class="client-tile-item item-{{ $itemIdx }} w-[148.5px] max-w-full h-[177px] rounded-[8px] bg-[#E1E1E1]/30 pointer-events-none shrink-0" style="margin-top: {{ $item['margin_top'] }}px;"></div>
            @else
              @php
                $cData = $testimonials[$item['idx']] ?? $testimonials[0];
              @endphp
              <div class="client-figma-tile client-tile-item item-{{ $itemIdx }} relative w-[148.5px] max-w-full h-[177px] rounded-[8px] overflow-hidden shadow-xs cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-lg group z-10 shrink-0" style="margin-top: {{ $item['margin_top'] }}px;" data-client-name="{{ $cData['name'] }}" data-client-role="{{ $cData['role'] }}" data-client-quote="{{ $cData['quote'] }}" role="button" tabindex="0" aria-label="Xem phản hồi của {{ $cData['name'] }}">
                <img class="w-full h-full object-cover rounded-[8px] transition-transform duration-500 group-hover:scale-110" src="{{ $cData['image'] }}" alt="{{ $cData['name'] }}" loading="lazy" decoding="async">
                <div class="absolute inset-0 bg-[#051650]/0 group-hover:bg-[#051650]/20 transition-colors rounded-[8px]"></div>
              </div>
            @endif
          @endforeach
        </div>
      @endforeach
    </div>

    {{-- Center Architectural Headline & Action Block (Được nâng lên top: 355px để khung dưới thoáng) --}}
    <div class="absolute left-1/2 -translate-x-1/2 w-full max-w-[800px] flex flex-col items-center text-center z-20 pointer-events-auto" style="top: 350px;">
      
      {{-- Brand Statement 3-Tier --}}
      <div class="flex flex-col items-center leading-none reveal-item reveal-delay-1" style="gap:10px">
        <div class="text-[49px] font-bold text-[#051650] uppercase tracking-tight leading-none" style="font-size:49px ">{{ $headTop }}</div>
        <div class="text-[15px] font-normal uppercase tracking-[0.26em] text-[#051650] py-1 leading-none" style="font-size:15px ">{{ $headMid }}</div>
        <div class="text-[49px] font-bold text-[#051650] uppercase tracking-tight leading-none" style="font-size:49px ">{{ $headBot }}</div>
      </div>

      {{-- Subtitle Statement --}}
      <div class="text-[16px] text-neutral-800 font-light max-w-[380px] pt-3 leading-relaxed text-center reveal-item reveal-delay-2" style="font-size: 16px;">
        {!! strip_tags($subText) !== $subText ? $subText : nl2br(e($subText)) !!}
      </div>

      {{-- CTA Button "NHẬN TƯ VẤN NGAY" --}}
      <div class="pt-6 reveal-item reveal-delay-3">
        <button class="inline-flex items-center justify-center gap-2.5 w-[231px] h-[38px] rounded-full bg-white text-[#051650] hover:bg-[#051650] hover:text-white border border-neutral-200/90 text-[13px] font-bold uppercase tracking-wider shadow-xs transition-all duration-300 cursor-pointer group" type="button" data-contact-modal-toggle aria-label="{{ $btnText }}" style="padding: 8px 20px;">
          <span class="text-sm transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
          <span>{{ $btnText }}</span>
        </button>
      </div>

    </div>

    {{-- Bottom Scroll Down Indicator (Định vị chuẩn bottom: 24px để luôn thấy rõ trên mọi màn hình) --}}
    <div class="absolute left-1/2 -translate-x-1/2 z-20 flex justify-center" style="bottom: 24px;">
      <button class="btn-scroll-indicator text-brand-navy hover:text-amber-600 transition-colors focus:outline-none flex items-center justify-center cursor-pointer" type="button" data-scroll-target="#home-news" aria-label="Cuộn xuống nội dung">
        <div class="scroll-indicator-wrap w-[32px] h-[32px] flex items-center justify-center text-[#051650]">
          <svg class="icon-bottom-svg" width="32" height="32" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path class="ring-inner" d="M17.9479 26.434C12.2209 27.6774 6.46305 24.2225 4.9143 18.5018L4.85684 18.2635C5.23233 18.4517 5.62336 18.609 6.02629 18.734C7.67677 23.805 13.0346 26.7432 18.2399 25.3341C23.6119 23.8798 26.7879 18.3457 25.3335 12.9737C23.9242 7.76852 18.6843 4.62471 13.4732 5.75791C13.162 5.47306 12.829 5.21478 12.477 4.98555C12.5552 4.96254 12.6327 4.93631 12.7118 4.9149L13.2661 4.78C18.8084 3.57702 24.3788 6.77438 26.1358 12.1667L26.2988 12.7124C27.8974 18.6174 24.4063 24.7007 18.5012 26.2994L17.9479 26.434Z" fill="currentColor"></path>
            <path class="ring-outer" d="M15.9999 0.039248C24.8365 0.0163306 32.0185 7.16117 32.0414 15.9977C32.0461 17.8066 31.7494 19.5458 31.2003 21.1688C30.9953 20.7836 30.7579 20.4187 30.4904 20.0779C30.8523 18.7808 31.0451 17.4132 31.0414 16.0003C31.0199 7.71605 24.2868 1.01776 16.0025 1.03924C7.7183 1.06073 1.02001 7.79385 1.04149 16.0781C1.06298 24.3623 7.7961 31.0606 16.0803 31.0391C17.4933 31.0355 18.8598 30.8356 20.155 30.4671C20.4972 30.7327 20.8634 30.9682 21.2496 31.1712C19.6295 31.7287 17.8918 32.0344 16.0829 32.0391C7.24641 32.0621 0.064413 24.9172 0.0414956 16.0807C0.0185782 7.24416 7.16342 0.0621654 15.9999 0.039248Z" fill="currentColor"></path>
            <path class="arrow-icon" d="M20.377 16.8984C20.3231 16.8337 20.2569 16.7803 20.1824 16.7412C20.1078 16.7022 20.0263 16.6782 19.9424 16.6707C19.8586 16.6632 19.7741 16.6723 19.6938 16.6975C19.6134 16.7227 19.5389 16.7634 19.4743 16.8175L16.69 19.1479L16.6703 11.5576C16.6699 11.3878 16.602 11.2252 16.4817 11.1055C16.3613 10.9858 16.1984 10.9188 16.0286 10.9192C15.8589 10.9197 15.6963 10.9875 15.5766 11.1078C15.4569 11.2282 15.3899 11.3911 15.3903 11.5609L15.41 19.1513L12.6136 16.8353C12.5487 16.7817 12.4739 16.7414 12.3935 16.7166C12.3131 16.6919 12.2285 16.6833 12.1448 16.6912C12.061 16.6992 11.9796 16.7235 11.9052 16.7629C11.8309 16.8023 11.765 16.856 11.7114 16.9208C11.6578 16.9857 11.6174 17.0605 11.5927 17.1409C11.568 17.2213 11.5594 17.3059 11.5673 17.3896C11.5753 17.4734 11.5996 17.5548 11.639 17.6292C11.6784 17.7035 11.7321 17.7694 11.7969 17.823L15.6452 21.013L15.7414 21.0704L15.8247 21.115C15.9731 21.1718 16.1374 21.1714 16.2855 21.1138L16.3686 21.0688L16.4644 21.0109L20.2961 17.801C20.3608 17.7471 20.4142 17.681 20.4532 17.6064C20.4923 17.5318 20.5163 17.4503 20.5238 17.3664C20.5313 17.2826 20.5222 17.1981 20.497 17.1178C20.4718 17.0375 20.431 16.9629 20.377 16.8984V16.8984Z" fill="currentColor"></path>
          </svg>
        </div>
      </button>
    </div>

  </div>

  {{-- Mobile & Tablet Responsive Flow (< 1024px) --}}
  <div class="block lg:hidden w-full flex-1 flex flex-col justify-between items-center px-4 pt-16 pb-8 gap-8">
    
    {{-- Mobile Centered Headline --}}
    <div class="flex flex-col items-center text-center gap-1">
      <div class="text-[28px] sm:text-[38px] font-bold text-[#051650] uppercase leading-none">{{ $headTop }}</div>
      <div class="text-[12px] sm:text-[14px] font-normal uppercase tracking-[0.24em] text-[#051650] py-0.5 leading-none">{{ $headMid }}</div>
      <div class="text-[28px] sm:text-[38px] font-bold text-[#051650] uppercase leading-none">{{ $headBot }}</div>
      <p class="text-[16px] text-neutral-700 font-light max-w-[360px] pt-2 leading-relaxed" style="font-size: 16px;">
        {!! strip_tags($subText) !== $subText ? $subText : nl2br(e($subText)) !!}
      </p>
      <div class="pt-3">
        <button class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-white text-[#051650] hover:bg-[#051650] hover:text-white border border-neutral-300 text-[12.5px] font-bold uppercase tracking-wider shadow-xs transition-colors cursor-pointer" type="button" data-contact-modal-toggle aria-label="{{ $btnText }}">
          <span>&rarr;</span>
          <span>{{ $btnText }}</span>
        </button>
      </div>
    </div>

    {{-- Mobile Staggered Photo Grid --}}
    <div class="w-full max-w-[600px] grid grid-cols-2 gap-3 sm:gap-4">
      @foreach($testimonials as $mItem)
        <div class="client-figma-tile relative aspect-[148/177] rounded-[8px] overflow-hidden shadow-xs cursor-pointer group" data-client-name="{{ $mItem['name'] }}" data-client-role="{{ $mItem['role'] }}" data-client-quote="{{ $mItem['quote'] }}">
          <img class="w-full h-full object-cover rounded-[8px]" src="{{ $mItem['image'] }}" alt="{{ $mItem['name'] }}" loading="lazy" decoding="async">
        </div>
      @endforeach
    </div>

  </div>

  {{-- Interactive Client Feedback Modal Box (Nằm góc bottom: 0, left: 0 - tinh tế, không che khuất màn hình) --}}
  <div id="client-feedback-popover" class="absolute left-0 bottom-0 z-40 p-4 sm:p-6 opacity-0 pointer-events-none transition-all duration-300 translate-y-3 max-w-[420px] w-full" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="bg-white/95 backdrop-blur-md rounded-[16px] w-full p-5 sm:p-6  flex flex-col gap-2 relative transition-all duration-300 transform scale-95" id="client-popover-card">
      <div class="flex items-start justify-between gap-3 pb-2.5 border-b border-neutral-100">
        <div class="flex flex-col gap-0.5 pr-2">
          <h3 id="client-popover-name" class="text-[16px] sm:text-[17px] font-bold text-[#051650] uppercase tracking-tight leading-snug"></h3>
          <p id="client-popover-role" class="text-[12px] sm:text-[12.5px] italic text-neutral-500 font-medium leading-tight"></p>
        </div>
        <button id="close-client-popover" class="shrink-0 w-8 h-8 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-600 hover:text-neutral-900 flex items-center justify-center text-xl font-bold transition-colors cursor-pointer select-none leading-none" type="button" aria-label="Đóng">&times;</button>
      </div>
      <p id="client-popover-quote" class="text-[16px] text-neutral-700 leading-relaxed pt-1.5" style="font-size: 16px;"></p>
    </div>
  </div>

</section>

@push('scripts')
<script>
  (function () {
    function initClientsSection() {
      const sec = document.getElementById('home-clients');
      if (!sec) return;

      const popover = document.getElementById('client-feedback-popover');
      const popoverCard = document.getElementById('client-popover-card');
      const closeBtn = document.getElementById('close-client-popover');
      const nameEl = document.getElementById('client-popover-name');
      const roleEl = document.getElementById('client-popover-role');
      const quoteEl = document.getElementById('client-popover-quote');

      const tiles = sec.querySelectorAll('.client-figma-tile');
      let activeTile = null;

      function showFeedback(tile, name, role, quote) {
        if (!popover || !nameEl || !roleEl || !quoteEl) return;
        nameEl.textContent = name;
        roleEl.textContent = role;
        quoteEl.textContent = quote;

        if (activeTile && activeTile !== tile) {
          activeTile.classList.remove('ring-4', 'ring-[#051650]', 'shadow-2xl', 'scale-105');
        }
        if (tile) {
          activeTile = tile;
          activeTile.classList.add('ring-4', 'ring-[#051650]', 'shadow-2xl', 'scale-105');
        }

        popover.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-3');
        popover.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
        popover.setAttribute('aria-hidden', 'false');
        if (popoverCard) popoverCard.classList.remove('scale-95');
      }

      function hideFeedback() {
        if (!popover) return;
        if (activeTile) {
          activeTile.classList.remove('ring-4', 'ring-[#051650]', 'shadow-2xl', 'scale-105');
          activeTile = null;
        }
        popover.classList.add('opacity-0', 'pointer-events-none', 'translate-y-3');
        popover.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
        popover.setAttribute('aria-hidden', 'true');
        if (popoverCard) popoverCard.classList.add('scale-95');
      }

      tiles.forEach(tile => {
        tile.addEventListener('click', function (e) {
          e.stopPropagation();
          const name = this.getAttribute('data-client-name') || '';
          const role = this.getAttribute('data-client-role') || '';
          const quote = this.getAttribute('data-client-quote') || '';
          showFeedback(this, name, role, quote);
        });

        tile.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const name = this.getAttribute('data-client-name') || '';
            const role = this.getAttribute('data-client-role') || '';
            const quote = this.getAttribute('data-client-quote') || '';
            showFeedback(this, name, role, quote);
          }
        });
      });

      if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          hideFeedback();
        });
      }

      document.addEventListener('click', function (e) {
        if (popover && !popover.contains(e.target) && !e.target.closest('.client-figma-tile')) {
          hideFeedback();
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') hideFeedback();
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initClientsSection);
    } else {
      initClientsSection();
    }
  })();
</script>
@endpush
