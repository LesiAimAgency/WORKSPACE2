<footer class="w-full bg-white border-t border-neutral-200/80 text-neutral-800 flex justify-center" id="main-site-footer" data-figma-id="212:2267">
  <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-[72px] py-10 sm:py-14 lg:py-[80px]">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 pb-12  grid-layout-footer">
      {{-- Cột 4: Thông tin công ty --}}
      <div class="lg:col-span-4 flex flex-col gap-5">
        <a class="inline-block transition-transform hover:scale-105" href="{{ route('home') }}" title="Đại Phúc 68">
          <img class="h-10 w-auto object-contain" src="{{ asset('themes/daiphuc68/assets/icons/logo-daiphuc68.svg') }}" alt="Đại Phúc 68" style="height: 6rem;">
        </a>
     
        <div class="text-[20px] md:text-[20px] text-neutral-600 flex flex-col gap-1.5 pt-2">
          <div class="font-bold text-neutral-600">{{ setting('company_name', 'CÔNG TY TNHH ĐẠI PHÚC 68') }}</div>
          <p class="leading-relaxed">Địa chỉ: {{ setting('contact_address', setting('company_address', '67 Huỳnh Thiện Lộc, Phường Tân Phú, Hồ Chí Minh, Việt Nam')) }}</p>
          <p>Mã số thuế: {{ setting('tax_code', '0000999988') }}</p>
          <p>Hotline: <a href="tel:{{ preg_replace('/[^0-9]/', '', setting('contact_phone', setting('company_phone', '0988 888 888'))) }}" class="hover:text-brand-navy">{{ setting('contact_phone', setting('company_phone', '0988 888 888')) }}</a></p>
        </div>
      </div>

      {{-- Cột 8: Phân tầng 2 lớp (Tầng 1: 3 cột link, Tầng 2: Bản quyền & Slogan) --}}
      <div class="lg:col-span-8 flex flex-col justify-between gap-8 lg:gap-12">
        {{-- Tầng 1: Các nhóm link (Liên kết nhanh, Chính sách, Social Media) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
          {{-- Nhóm 1: Liên kết nhanh --}}
          <div class="flex flex-col gap-4">
            <h4 class="text-[15px] md:text-[16px] font-bold text-neutral-900 tracking-wide">Liên kết nhanh</h4>
            <ul class="flex flex-col gap-2.5 text-[14px] text-neutral-600">
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ route('home') }}">Trang chủ</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ route('about-us') }}">Về chúng tôi</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ route('products.index') }}">Sản phẩm</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ route('projects.index') }}">Dự án</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ route('news.index') }}">Tin tức</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ route('contact.page') }}">Liên hệ</a></li>
            </ul>
          </div>

          {{-- Nhóm 2: Chính sách --}}
          <div class="flex flex-col gap-4">
            <h4 class="text-[15px] md:text-[16px] font-bold text-neutral-900 tracking-wide">Chính sách</h4>
            <ul class="flex flex-col gap-2.5 text-[14px] text-neutral-600">
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ url('/dieu-khoan-su-dung') }}">Điều khoản sử dụng</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ url('/chinh-sach-bao-mat') }}">Chính sách bảo mật</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="#chinh-sach-ban-hang">Chính sách bán hàng</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="#chinh-sach-dai-ly">Chính sách đại lý</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="#chinh-sach-bao-hanh">Chính sách bảo hành</a></li>
              <li><a class="hover:text-brand-navy hover:underline transition-colors" href="{{ route('contact.page') }}#faq">Câu hỏi thường gặp</a></li>
            </ul>
          </div>

          {{-- Nhóm 3: Social Media --}}
          @php
            $activeFooterSocials = function_exists('get_social_items') ? get_social_items(true) : [];
          @endphp
          <div class="flex flex-col gap-4">
            <h4 class="text-[15px] md:text-[16px] font-bold text-neutral-900 tracking-wide">Social Media</h4>
            <div class="grid grid-cols-2 gap-y-2.5 gap-x-2 text-[14px] text-neutral-600">
              @foreach($activeFooterSocials as $s)
                @php
                  $sName = $s['name'] ?? 'Social';
                  $sLink = $s['link'] ?? '#';
                  $sTarget = !empty($s['target']) ? $s['target'] : '_blank';
                @endphp
                <a class="hover:text-brand-navy hover:underline transition-colors flex items-center gap-2 truncate" 
                   href="{{ $sLink }}" 
                   target="{{ $sTarget }}" 
                   rel="noopener noreferrer" 
                   title="{{ $sName }}">
                  <span class="truncate">{{ $sName }}</span>
                </a>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Tầng 2: Bản quyền & Slogan đưa vào trong cột 8 --}}
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[13px] text-neutral-500">
          <div class="sm:text-center">&copy; {{ date('Y') }} CÔNG TY TNHH ĐẠI PHÚC 68. All rights reserved.</div>
          <div class="font-medium text-neutral-600">Xây Dựng Giá Trị - Kiến Tạo Tương Lai</div>
        </div>
      </div>
    </div>
  </div>
</footer>
