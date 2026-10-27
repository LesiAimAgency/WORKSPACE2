@props(['items' => []])

<nav class="w-full bg-white border-b border-neutral-200 shadow-2xs relative z-20 flex justify-center" aria-label="Breadcrumb navigation">
  <div class="w-full max-w-[1440px] flex items-center justify-center p-6 sm:p-8">
    <div class="inline-flex items-center gap-4 text-[15px] sm:text-[16px] font-normal text-brand-navy uppercase tracking-wider flex-wrap justify-center gap-x-3 sm:gap-x-4 md:gap-x-6 gap-y-2">
      <a class="text-brand-navy hover:text-neutral-600 transition-colors inline-flex items-center" href="{{ route('home') }}" aria-label="Trang chủ">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
      </a>
      @foreach($items as $item)
        <span class="h-5 w-[1px] bg-[#051650]"></span>
        @if(!empty($item['url']) && !$loop->last)
          <a class="text-brand-navy hover:text-neutral-600 transition-colors" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
          <span class="text-brand-navy text-[15px] sm:text-[16px] font-normal truncate max-w-[280px] sm:max-w-none">{{ $item['label'] }}</span>
        @endif
      @endforeach
    </div>
  </div>
</nav>
