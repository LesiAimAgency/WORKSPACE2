@php
  $titleLine1 = $title_line_1 ?? ($settings['title_line_1'] ?? ($eyebrow ?? ($settings['eyebrow'] ?? null)));
  $titleLine2 = $title_line_2 ?? ($settings['title_line_2'] ?? ($title ?? ($settings['title'] ?? null)));

  if (empty($titleLine1) && empty($titleLine2)) {
    $titleLine1 = 'ĐẠI PHÚC 68';
    $titleLine2 = 'GIẢI PHÁP XÂY DỰNG TOÀN DIỆN';
  } elseif (empty($titleLine1) && !empty($titleLine2)) {
    if (str_contains($titleLine2, "\n")) {
      $parts = explode("\n", str_replace("\r", "", $titleLine2), 2);
      $titleLine1 = trim($parts[0]);
      $titleLine2 = trim($parts[1] ?? '');
    } elseif (str_contains(mb_strtoupper($titleLine2), 'GIẢI PHÁP')) {
      $pos = mb_stripos($titleLine2, 'GIẢI PHÁP');
      $titleLine1 = trim(mb_substr($titleLine2, 0, $pos));
      $titleLine2 = trim(mb_substr($titleLine2, $pos));
    } else {
      $titleLine1 = 'ĐẠI PHÚC 68';
    }
  }

  $desc1 = $description ?? ($settings['description'] ?? 'Đại Phúc 68 là đơn vị hoạt động trong lĩnh vực thiết kế và xây dựng, cung cấp giải pháp toàn diện từ tư vấn, thiết kế đến thi công các công trình. Với định hướng lấy chất lượng, sự tận tâm và uy tín làm nền tảng, chúng tôi luôn chú trọng từng chi tiết trong mỗi dự án nhằm tạo nên những không gian bền vững, thẩm mỹ và phù hợp với nhu cầu thực tế của khách hàng.');
  $desc2 = $description_2 ?? ($settings['description_2'] ?? 'Sở hữu đội ngũ giàu kinh nghiệm cùng quy trình làm việc chuyên nghiệp, Đại Phúc 68 không ngừng cập nhật xu hướng thiết kế, ứng dụng giải pháp và vật liệu phù hợp để tối ưu công năng, chi phí và giá trị công trình. Chúng tôi tin rằng mỗi công trình không chỉ là một sản phẩm xây dựng, mà còn là sự kết tinh của ý tưởng, tâm huyết và những giá trị lâu dài.');
@endphp

<section class="relative w-full bg-[#F6F6F6] text-[#051650] flex flex-col items-center justify-center select-none box-border" id="about-overview" aria-label="Giải Pháp Xây Dựng Toàn Diện - Đại Phúc 68">
  <div class="about-overview-grid-container w-full max-w-[1440px] py-10 sm:py-14 lg:py-20 px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 2xl:px-[258px] box-border flex flex-col items-start gap-6 sm:gap-8">
    {{-- Header: 2 Dòng Tiêu Đề chuẩn Typography --}}
    <div class="w-full flex flex-col items-start text-left gap-1.5 sm:gap-2 reveal-item reveal-delay-1">
      @if(!empty($titleLine1))
        <h2 class="about-overview-title-line-1 text-[24px] sm:text-[34px] md:text-[42px] lg:text-[49px] font-semibold uppercase tracking-tight text-[#051650] leading-tight">
          {{ $titleLine1 }}
        </h2>
      @endif  
      @if(!empty($titleLine2))
        <h2 class="about-overview-title-line-2 text-[20px] sm:text-[24px] md:text-[28px] lg:text-[31px] font-medium uppercase tracking-tight text-[#051650] leading-tight">
          {{ $titleLine2 }}
        </h2>
      @endif
    </div>

    {{-- Content Paragraphs --}}
    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-10 text-[16px] sm:text-[16px] leading-relaxed text-[#333333] reveal-item reveal-delay-2">
      <div class="text-justify md:text-justify">
        {!! strip_tags($desc1) !== $desc1 ? $desc1 : nl2br(e($desc1)) !!}
      </div>
      <div class="text-justify md:text-justify">
        {!! strip_tags($desc2) !== $desc2 ? $desc2 : nl2br(e($desc2)) !!}
      </div>
    </div>
  </div>
</section>
