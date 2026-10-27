@props(['product', 'variant' => 'wide', 'btnText' => null])

@php
  $meta = is_array($product->meta_data) ? $product->meta_data : json_decode($product->meta_data ?? '[]', true);
  $priceVal = (int) ($meta['price'] ?? 0);
  $displayPrice = ($product instanceof \App\Models\Post) ? ($product->display_price !== 'Liên hệ' ? $product->display_price : 'Giá liên hệ dự án') : ($priceVal > 0 ? number_format($priceVal, 0, ',', '.') . 'đ' : ($product->display_price ?? 'Giá liên hệ dự án'));
  $badge = $meta['badge'] ?? null;
  $brandName = $product->brand ?: ($meta['brand'] ?? 'Đại Phúc 68');
  $brandLogo = $product->brand_logo ?: (!empty($meta['brand_logo']) ? (str_starts_with($meta['brand_logo'], 'http') ? $meta['brand_logo'] : asset($meta['brand_logo'])) : null);
  $imageSrc = $product->featured_image ?: asset('storage/media/products/product-cau-chi-tu-roi-vina-electric.png');
  $productUrl = !empty($product->slug) ? route('products.show', $product->slug) : ($product->url ?? '#');
  $isCompact = ($variant === 'compact');
  $boxClass = $isCompact ? 'product-box-compact' : 'product-box-wide';
  $figmaVariant = $isCompact ? 'Compact-236x395' : 'Wide-334x482';
  $imgHeightClass = $isCompact ? 'aspect-[236/240] h-auto' : 'h-[130px] xs:h-[155px] sm:h-[220px] md:h-[250px] lg:h-[284px]';
@endphp

<article class="{{ $boxClass }} w-full max-w-full h-full bg-white rounded-[8px] p-2 sm:p-2.5 md:p-[10px] shadow-sm border border-neutral-200/80 flex flex-col justify-between hover:shadow-md transition-shadow group" data-figma-variant="{{ $figmaVariant }}" data-product-id="{{ $product->id ?? '' }}">
  <div class="product-image-box relative w-full shrink-0 bg-[#F7F7F7] rounded-[4px] flex items-center justify-center p-2 sm:p-3.5 md:p-4 overflow-hidden {{ $imgHeightClass }}">
    @if(!empty($badge))
      <span class="product-badge hidden absolute top-1.5 left-1.5 sm:top-2 sm:left-2 z-10 px-1.5 sm:px-2 py-0.5 rounded text-[8px] sm:text-[9px] font-bold uppercase tracking-wider {{ str_contains(mb_strtolower($badge), 'bán chạy') ? 'bg-red-600 text-white' : 'bg-brand-navy text-white' }}" style="display: none;">
        {{ $badge }}
      </span>
    @endif
    <a href="{{ $productUrl }}" class="product-img-link w-full h-full flex items-center justify-center" aria-label="{{ $product->title }}">
      <img class="product-img w-full h-full object-contain max-h-full max-w-full mix-blend-multiply select-none transition-transform duration-300 group-hover:scale-105" 
           src="{{ $imageSrc }}" 
           alt="{{ $product->title }}" 
           loading="lazy" 
           onerror="this.onerror=null; this.src='{{ asset('themes/daiphuc68/assets/icons/logo-daiphuc68.svg') }}'; this.classList.add('opacity-30', 'p-8');" />
    </a>
    <button class="btn-wishlist hidden absolute top-1.5 right-1.5 sm:top-2 sm:right-2 md:top-[10px] md:right-[10px] w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-brand-navy hover:bg-[#0c2370] text-white flex items-center justify-center shadow-sm transition-colors focus:outline-none z-10" style="display: none;" type="button" aria-label="Thêm vào danh sách yêu thích" title="Yêu thích">
      <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 fill-white" viewBox="0 0 21 18">
        <path d="M10.5321 17.8603C10.394 17.8615 10.2569 17.8353 10.1289 17.7834C10.0009 17.7315 9.88431 17.6548 9.78596 17.5578L1.6074 9.41107C0.581489 8.3798 0.00391426 6.98539 0.000141652 5.53075C-0.00363095 4.0761 0.566703 2.67872 1.58725 1.64214C2.61582 0.611142 4.01123 0.0300579 5.46755 0.0262809C6.92388 0.022504 8.32228 0.596343 9.35618 1.62199L10.493 2.75289L11.6239 1.61611C12.6524 0.585113 14.0478 0.00402805 15.5042 0.000251087C16.9605 -0.00352588 18.3589 0.570313 19.3928 1.59596C20.4187 2.62723 20.9963 4.02164 21.0001 5.47628C21.0038 6.93093 20.4335 8.32832 19.4129 9.36489L11.2768 17.5539C11.1789 17.6515 11.0628 17.7288 10.935 17.7813C10.8072 17.8339 10.6703 17.8608 10.5321 17.8603Z"></path>
      </svg>
    </button>
  </div>
  <div class="product-body pt-2 sm:pt-3 flex flex-col flex-grow justify-between gap-2 sm:gap-2.5">
    <div class="product-title-wrap min-h-[36px] sm:min-h-[44px] md:min-h-[52px] flex items-start">
      <h3 class="product-title text-[12px] xs:text-[13px] sm:text-[16px] md:text-[18px] lg:text-[19px] font-medium text-black leading-[17px] xs:leading-[19px] sm:leading-[22px] md:leading-[24px] line-clamp-2 hover:text-brand-navy transition-colors" title="{{ $product->title }}">
        <a class="focus:outline-none focus:underline" href="{{ $productUrl }}">{{ $product->title }}</a>
      </h3>
    </div>
    <div class="product-brand-row h-7 sm:h-8 flex items-center gap-1.5 sm:gap-2 overflow-hidden w-full">
      <span class="brand-tag inline-flex items-center px-1.5 sm:px-2.5 md:px-3 py-0.5 rounded-full border border-black text-[9px] xs:text-[10px] sm:text-[11px] md:text-[12px] font-normal text-black tracking-tight shrink-0 select-none">Thương hiệu</span>
      <a class="brand-link inline-flex items-center min-w-0 max-w-[85px] sm:max-w-[120px] md:max-w-[150px] hover:opacity-80 transition-opacity" href="#brand" title="{{ $brandName }}">
        @if(!empty($brandLogo))
          <img class="brand-logo h-5 sm:h-6 md:h-7 max-w-[70px] sm:max-w-[90px] md:max-w-[110px] w-auto object-contain shrink-0" src="{{ $brandLogo }}" alt="{{ $brandName }}" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
          <span class="brand-text-fallback hidden text-[10px] sm:text-xs font-bold text-brand-navy truncate px-1.5 sm:px-2.5 md:px-3 py-0.5 rounded bg-neutral-100">{{ $brandName }}</span>
        @else
          <span class="brand-text-fallback text-[10px] sm:text-xs font-bold text-brand-navy truncate px-1.5 sm:px-2.5 md:px-3 py-0.5 rounded bg-neutral-100">{{ $brandName }}</span>
        @endif
      </a>
    </div>
    <div class="product-price-wrap min-h-[22px] sm:min-h-[28px] flex items-baseline gap-1.5 sm:gap-2 flex-wrap text-[12.5px] xs:text-[14px] sm:text-[18px] md:text-[20px] font-medium text-black tracking-tight leading-none pt-0.5">
      <span class="price-current text-[12.5px] xs:text-[14px] sm:text-[16px] md:text-[18px] lg:text-[19px] font-medium text-black">{{ $displayPrice }}</span>
    </div>
    <div class="product-actions pt-2 w-full flex items-center gap-1.5 sm:gap-2">
      <a href="{{ $productUrl }}" class="btn-action flex-1 inline-flex items-center justify-center rounded-full bg-white hover:bg-neutral-100 text-[#051650] border border-[#051650]/40 font-bold uppercase tracking-wider transition-colors shadow-2xs whitespace-nowrap min-w-0 py-1.5 px-1 sm:py-2 sm:px-2 text-[9px] xs:text-[10px] sm:text-[11px] md:text-[12px] gap-1" title="Xem chi tiết sản phẩm">
        <span class="truncate">{{ $btnText ?? 'CHI TIẾT' }}</span>
      </a>
      <button type="button" 
              class="btn-quote flex-1 inline-flex items-center justify-center rounded-full bg-[#051650] hover:bg-[#0c2370] text-white font-bold uppercase tracking-wider transition-colors shadow-xs whitespace-nowrap min-w-0 py-1.5 px-1 sm:py-2 sm:px-2 text-[9px] xs:text-[10px] sm:text-[11px] md:text-[12px] gap-1 cursor-pointer"
              data-open-quote-modal
              data-product-title="{{ $product->title }}"
              data-product-image="{{ $imageSrc }}"
              data-product-sku="{{ $product->sku ?? '' }}"
              data-product-price="{{ $displayPrice }}"
              title="Yêu cầu báo giá {{ $product->title }}">
        <span class="truncate">BÁO GIÁ</span>
      </button>
    </div>
  </div>
</article>
