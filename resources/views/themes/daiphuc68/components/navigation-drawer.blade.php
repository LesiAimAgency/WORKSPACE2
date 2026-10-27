<div class="mobile-nav-overlay" id="mobile-nav-overlay" aria-hidden="true"></div>
<nav class="mobile-nav-drawer" id="mobile-nav-drawer" aria-label="Menu điều hướng chính" role="dialog" aria-modal="true">
  <div class="flex items-center justify-start pb-8">
    <button class="inline-flex items-center gap-3 text-xs uppercase font-bold text-neutral-800 hover:text-[#EC460B] transition-colors cursor-pointer py-1 group focus:outline-none" id="close-drawer-btn" type="button" aria-label="Đóng menu">
      <span class="w-6 h-[1.5px] bg-neutral-800 group-hover:w-8 group-hover:bg-[#EC460B] transition-all"></span>
      <span class="tracking-wider">Close</span>
    </button>
  </div>
  <div class="flex-1 flex flex-col justify-start gap-8">
    <ul class="flex flex-col gap-6 pt-2">
      <li>
        <a class="nav-drawer-link block text-[22px] sm:text-[24px] font-medium text-[#051650] uppercase tracking-tight hover:translate-x-1.5 transition-all" href="{{ route('home') }}" data-nav-link>TRANG CHỦ</a>
      </li>
      <li>
        <a class="nav-drawer-link block text-[22px] sm:text-[24px] font-medium text-[#051650] uppercase tracking-tight hover:translate-x-1.5 transition-all" href="{{ route('about-us') }}" data-nav-link>VỀ CHÚNG TÔI</a>
      </li>
      <li>
        <a class="nav-drawer-link block text-[22px] sm:text-[24px] font-medium text-[#051650] uppercase tracking-tight hover:translate-x-1.5 transition-all" href="{{ route('products.index') }}" data-nav-link>SẢN PHẨM</a>
      </li>
      <li>
        <a class="nav-drawer-link block text-[22px] sm:text-[24px] font-medium text-[#051650] uppercase tracking-tight hover:translate-x-1.5 transition-all" href="{{ route('projects.index') }}" data-nav-link>DỰ ÁN</a>
      </li>
      <li>
        <a class="nav-drawer-link block text-[22px] sm:text-[24px] font-medium text-[#051650] uppercase tracking-tight hover:translate-x-1.5 transition-all" href="{{ route('news.index') }}" data-nav-link>TIN TỨC</a>
      </li>
      <li>
        <a class="nav-drawer-link block text-[22px] sm:text-[24px] font-medium text-[#051650] uppercase tracking-tight hover:translate-x-1.5 transition-all" href="{{ route('contact.page') }}" data-nav-link>LIÊN HỆ</a>
      </li>
    </ul>
 
  </div>
  <div class="pt-8 border-t border-neutral-200/80 flex flex-col gap-3.5">
    <div class="text-[11px] sm:text-xs font-semibold uppercase tracking-widest text-neutral-400">THÔNG TIN LIÊN HỆ</div>
    <div class="flex flex-col gap-2.5 text-xs sm:text-sm text-neutral-700">
      <div class="flex items-center gap-3">
        <svg class="w-4 h-4 text-neutral-800 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
        <a class="hover:text-[#051650] transition-colors" href="mailto:{{ setting('contact_email', setting('company_email', 'contact@daiphuc68.vn')) }}">{{ setting('contact_email', setting('company_email', 'contact@daiphuc68.vn')) }}</a>
      </div>
      <div class="flex items-center gap-3">
        <svg class="w-4 h-4 text-neutral-800 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
        <a class="hover:text-[#051650] transition-colors" href="tel:{{ preg_replace('/[^0-9]/', '', setting('contact_phone', setting('company_phone', '0988 888 888'))) }}">{{ setting('contact_phone', setting('company_phone', '0988 888 888')) }}</a>
      </div>
      <div class="flex items-start gap-3">
        <svg class="w-4 h-4 text-neutral-800 shrink-0 pt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
        <span>{{ setting('contact_address', setting('company_address', '67 Huỳnh Thiện Lộc, P. Tân Phú, TP. HCM')) }}</span>
      </div>
    </div>
  </div>
</nav>
