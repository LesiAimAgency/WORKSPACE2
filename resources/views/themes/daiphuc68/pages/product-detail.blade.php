@extends('themes.daiphuc68.layouts.app')

@php
  $meta = is_array($product->meta_data) ? $product->meta_data : json_decode($product->meta_data ?? '[]', true);
  $isVariable = ($product instanceof \App\Models\Post) ? $product->isVariable() : false;
  
  $priceVal = (int) ($meta['price'] ?? 0);
  $salePriceVal = (int) ($meta['sale_price'] ?? 0);
  
  if ($isVariable && $product instanceof \App\Models\Post) {
      $displayPrice = $product->display_price !== 'Liên hệ' ? $product->display_price : 'Giá liên hệ dự án';
      $oldPrice = null;
  } else {
      if ($salePriceVal > 0 && $priceVal > $salePriceVal) {
          $displayPrice = number_format($salePriceVal, 0, ',', '.') . '₫';
          $oldPrice = number_format($priceVal, 0, ',', '.') . '₫';
      } else {
          $displayPrice = $priceVal > 0 ? number_format($priceVal, 0, ',', '.') . '₫' : 'Giá liên hệ dự án';
          $oldPrice = null;
      }
  }

  $brandName = $product->brand ?: ($meta['brand'] ?? 'Đại Phúc 68');
  $brandLogo = $product->brand_logo;
  $productCode = $product->sku ?: ($meta['code'] ?? ($meta['sku'] ?? ('DP-' . str_pad((string)$product->id, 4, '0', STR_PAD_LEFT))));
  $categoryName = $product->category_name ?: 'Vật Tư Thiết Bị Điện';
  
  $gallery = $product->gallery;
  if (empty($gallery)) {
      $gallery = [
          $product->featured_image ?: asset('storage/media/products/product1.png'),
          asset('storage/media/products/product2.png'),
          asset('storage/media/products/product3.png'),
          asset('storage/media/products/product4.png'),
      ];
  }

  $specs = $product->specs;
  $documents = $product->documents;

  // Variation attributes: check product variation_attributes, fallback to meta
  $rawVariationAttrs = !empty($product->variation_attributes) ? $product->variation_attributes : ($meta['variation_attributes'] ?? []);
  if (empty($rawVariationAttrs) && !empty($meta['attributes'])) {
      $rawVariationAttrs = collect($meta['attributes'])->filter(fn($a) => !empty($a['variation']) && !empty($a['name']) && !empty($a['values']))->values()->all();
  }

  // Load global taxonomy attributes to retrieve configured display types ('select', 'button', 'color') and colors
  $globalTaxAttributes = \App\Models\Taxonomy::attributes()->with(['children' => fn($q) => $q->orderBy('order')])->get();
  $taxMapByName = [];
  $taxMapBySlug = [];
  foreach ($globalTaxAttributes as $tax) {
      $taxType = $tax->meta_data['type'] ?? 'button';
      $childColorMap = [];
      foreach ($tax->children as $child) {
          $childColorMap[mb_strtolower(trim($child->name))] = $child->meta_data['color_code'] ?? null;
      }
      $taxInfo = [
          'type' => $taxType,
          'colors' => $childColorMap,
      ];
      $taxMapByName[mb_strtolower(trim($tax->name))] = $taxInfo;
      $taxMapBySlug[$tax->slug] = $taxInfo;
  }

  $variationAttrs = collect($rawVariationAttrs)->map(function($attr) use ($taxMapByName, $taxMapBySlug) {
      $name = $attr['name'] ?? '';
      $slug = $attr['slug'] ?? \Illuminate\Support\Str::slug($name);
      $rawValues = $attr['values'] ?? [];
      if (is_string($rawValues)) {
          $rawValues = array_filter(array_map('trim', explode(',', $rawValues)));
      }
      
      $taxInfo = $taxMapBySlug[$slug] ?? ($taxMapByName[mb_strtolower(trim($name))] ?? null);
      $type = $attr['type'] ?? ($taxInfo['type'] ?? 'button');
      
      $cleanValues = [];
      $valueList = [];
      foreach ($rawValues as $val) {
          $valName = is_array($val) ? ($val['name'] ?? '') : (string)$val;
          $valColor = is_array($val) ? ($val['color_code'] ?? null) : null;
          if (!$valColor && isset($taxInfo['colors'][mb_strtolower(trim($valName))])) {
              $valColor = $taxInfo['colors'][mb_strtolower(trim($valName))];
          }
          $cleanValues[] = $valName;
          $valueList[] = [
              'name' => $valName,
              'color_code' => $valColor,
          ];
      }

      return [
          'name' => $name,
          'slug' => $slug,
          'type' => in_array($type, ['select', 'button', 'color']) ? $type : 'button',
          'values' => $cleanValues,
          'value_items' => $valueList,
      ];
  })->filter(fn($a) => !empty($a['name']) && !empty($a['values']))->values();

  $allAttributes = $meta['attributes'] ?? [];
  $descriptiveAttrs = collect($allAttributes)->filter(fn($a) => empty($a['variation']) && !empty($a['name']) && !empty($a['values']))->values();

  // Prepare Variations JSON payload
  $variationsData = [];
  if ($isVariable && $product instanceof \App\Models\Post) {
      $variationsData = $product->variations->where('status', 'active')->map(function($v) {
          $price = (float)$v->price;
          $salePrice = (float)($v->sale_price ?? 0);
          $effective = (float)$v->effective_price;
          return [
              'id' => $v->id,
              'sku' => $v->sku,
              'price' => $price,
              'sale_price' => $salePrice,
              'effective_price' => $effective,
              'formatted_price' => $effective > 0 ? number_format($effective, 0, ',', '.') . '₫' : 'Giá liên hệ',
              'formatted_regular_price' => ($salePrice > 0 && $price > $salePrice) ? number_format($price, 0, ',', '.') . '₫' : null,
              'manage_stock' => (bool)$v->manage_stock,
              'stock_quantity' => (int)$v->stock_quantity,
              'stock_status' => $v->stock_status,
              'is_in_stock' => (bool)$v->is_in_stock,
              'image' => $v->image ?: null,
              'attributes' => (array)($v->attributes ?? []),
          ];
      })->values()->all();
  }
@endphp

@php
  $seoTitle = !empty(trim((string)$product->meta_title)) 
      ? trim($product->meta_title) 
      : ($product->title . ' - ' . setting('site_title', 'CÔNG TY TNHH ĐẠI PHÚC 68'));

  $rawSeoDesc = !empty(trim((string)$product->meta_description)) 
      ? trim($product->meta_description) 
      : (!empty($product->excerpt) ? $product->excerpt : ('Chi tiết sản phẩm ' . $product->title . '. Cung cấp thiết bị điện chính hãng, chuẩn EVN & IEC từ Đại Phúc 68.'));
  $seoDescription = Str::limit(strip_tags(html_entity_decode((string)$rawSeoDesc, ENT_QUOTES, 'UTF-8')), 200);
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDescription)

@section('content')
  {{-- Hero Banner --}}
  <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden bg-brand-navy flex items-end justify-center pb-12 md:pb-16 text-white" aria-label="Hero Banner SẢN PHẨM CHI TIẾT">
    <div class="absolute inset-0 z-0">
      <img class="w-full h-full object-cover object-center filter brightness-95" src="{{ asset('storage/media/banners/hero-banner-main.jpg') }}" alt="{{ $product->title }}"/>
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-black/40"></div>
    </div>
    <div class="relative z-10 w-full max-w-[1110px] px-4 text-center">
      <h1 class="text-[26px] sm:text-[34px] md:text-[40px] lg:text-[44px] font-bold uppercase tracking-wider text-white drop-shadow-md">
         {{ $product->title }}
      </h1>
    </div>
  </section>

  {{-- Breadcrumb --}}
  @include('themes.daiphuc68.components.breadcrumb', [
    'items' => [
      ['label' => 'SẢN PHẨM', 'url' => route('products.index')],
      ['label' => mb_strtoupper($product->title), 'active' => true]
    ]
  ])

  {{-- Product Showcase Section --}}
  <section class="py-10 md:py-14 bg-white flex justify-center">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-16">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12 items-start">
        
        {{-- Left: Galleries --}}
        <div class="lg:col-span-6 xl:col-span-6 flex flex-col-reverse sm:flex-row gap-4 sm:gap-5 items-stretch">
          <div class="flex sm:flex-col gap-3 sm:gap-3.5 shrink-0 overflow-x-auto sm:overflow-visible pb-2 sm:pb-0 select-none" id="product-thumbs-container">
            @foreach($gallery as $index => $img)
              <button class="product-thumb-btn {{ $index === 0 ? 'active' : '' }} w-20 h-20 sm:w-[100px] sm:h-[100px] lg:w-[115px] lg:h-[115px] bg-white rounded-xl p-2.5 {{ $index === 0 ? 'border-2 border-brand-navy ring-2 ring-brand-navy/10' : 'hover:border-neutral-400' }} flex items-center justify-center overflow-hidden transition-all shadow-xs shrink-0 cursor-pointer" type="button" data-slide-index="{{ $index }}" data-img-src="{{ $img }}" aria-label="Xem ảnh {{ $index + 1 }}">
                <img class="w-full h-full object-contain mix-blend-multiply transition-opacity {{ $index === 0 ? 'opacity-100' : 'opacity-70 hover:opacity-100' }}" src="{{ $img }}" alt="{{ $product->title }} - Ảnh {{ $index + 1 }}">
              </button>
            @endforeach
          </div>

          <div class="flex-1 min-h-[380px] sm:min-h-[460px] lg:min-h-[500px] bg-white rounded-2xl border border-neutral-200 p-4 sm:p-6 lg:p-8 flex flex-col justify-center relative overflow-hidden group shadow-sm">
            <div class="swiper product-gallery-main-swiper w-full h-full flex items-center justify-center">
              <div class="swiper-wrapper items-center">
                @foreach($gallery as $index => $img)
                  <div class="swiper-slide flex items-center justify-center h-full">
                    <img class="w-full h-full object-contain max-h-[440px] mix-blend-multiply group-hover:scale-105 transition-transform duration-500 select-none cursor-zoom-in" src="{{ $img }}" alt="{{ $product->title }} - Chi tiết {{ $index + 1 }}">
                  </div>
                @endforeach
              </div>
            </div>
            <button class="product-gallery-prev absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white shadow-md border border-neutral-200 text-neutral-700 hover:text-brand-navy flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 z-10 cursor-pointer focus:outline-none" type="button" aria-label="Xem ảnh trước">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <button class="product-gallery-next absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white shadow-md border border-neutral-200 text-neutral-700 hover:text-brand-navy flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 z-10 cursor-pointer focus:outline-none" type="button" aria-label="Xem ảnh tiếp theo">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

        {{-- Right: Product Details & Purchase Actions --}}
        <div x-data="productDetailBox({
          isVariable: {{ $isVariable ? 'true' : 'false' }},
          defaultPrice: '{{ $displayPrice }}',
          defaultOldPrice: '{{ $oldPrice ?? '' }}',
          defaultSku: '{{ $productCode }}',
          defaultStockStatus: '{{ $product->stock_status }}',
          defaultStockQuantity: {{ (int)$product->stock_quantity }},
          defaultManageStock: {{ $product->manage_stock ? 'true' : 'false' }},
          variations: @js($variationsData),
          variationAttributes: @js($variationAttrs),
        })" class="lg:col-span-6 xl:col-span-6 flex flex-col gap-4">
          <div>
            <span class="inline-flex items-center px-3.5 py-0.5 rounded-full border border-[#051650] text-[#051650] text-xs font-semibold bg-white tracking-wide">
              {{ $categoryName }}
            </span>
          
          </div>

          <h2 class="text-2xl sm:text-3xl font-bold text-[#051650] leading-snug tracking-tight">
            {{ $product->title }}
          </h2>

        

          {{-- Variable Product: Attributes Selector --}}
          @if($isVariable && $variationAttrs->isNotEmpty())
            <div class="py-3 flex flex-col gap-4 border-t border-b border-neutral-200/90 bg-neutral-50/50 p-4 rounded-xl">
              <div class="text-xs font-bold uppercase tracking-wider text-[#051650] flex items-center justify-between">
                <span>LỰA CHỌN PHIÊN BẢN</span>
                <span class="text-[11px] font-normal text-neutral-500" x-show="matchedVariation" x-cloak>
                  SKU Biến thể: <strong class="text-brand-navy" x-text="matchedVariation ? matchedVariation.sku : ''"></strong>
                </span>
              </div>

              @foreach($variationAttrs as $attr)
                <div class="flex flex-col gap-2">
                  <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-neutral-700">{{ $attr['name'] }}:</span>
                    <span class="text-brand-navy font-bold" x-text="selected['{{ $attr['name'] }}'] || 'Chưa chọn'"></span>
                  </div>

                  @if($attr['type'] === 'select')
                    {{-- Kiểu hiển thị 1: Danh sách chọn (Select Dropdown) --}}
                    <div class="relative">
                      <select
                        x-model="selected['{{ $attr['name'] }}']"
                        @change="selectAttribute('{{ $attr['name'] }}', $event.target.value)"
                        class="w-full text-xs sm:text-sm font-medium border border-neutral-300 rounded-lg px-3.5 py-2.5 bg-white text-neutral-800 focus:outline-none focus:ring-2 focus:ring-[#051650]/20 focus:border-[#051650] transition-all cursor-pointer shadow-2xs appearance-none pr-10">
                        <option value="" disabled>-- Chọn {{ $attr['name'] }} --</option>
                        @foreach($attr['values'] as $val)
                          <option value="{{ $val }}" :disabled="!isOptionAvailable('{{ $attr['name'] }}', '{{ $val }}')">
                            {{ $val }}
                          </option>
                        @endforeach
                      </select>
                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-neutral-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                      </div>
                    </div>

                  @elseif($attr['type'] === 'color')
                    {{-- Kiểu hiển thị 2: Mã màu (Color Picker / Swatch) --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                      @foreach($attr['value_items'] as $item)
                        @php
                          $valName = $item['name'];
                          $valColor = $item['color_code'] ?: '#cbd5e1';
                          $isLight = in_array(strtolower($valColor), ['#ffffff', '#fff', '#f8fafc', '#f1f5f9', 'white']);
                        @endphp
                        <button type="button"
                          @click="selectAttribute('{{ $attr['name'] }}', '{{ $valName }}')"
                          :class="{
                            'ring-2 ring-offset-2 ring-[#051650] scale-110 shadow-sm': selected['{{ $attr['name'] }}'] === '{{ $valName }}',
                            'border border-neutral-300 hover:scale-105 opacity-90 hover:opacity-100': selected['{{ $attr['name'] }}'] !== '{{ $valName }}' && isOptionAvailable('{{ $attr['name'] }}', '{{ $valName }}'),
                            'cursor-not-allowed opacity-30 relative': !isOptionAvailable('{{ $attr['name'] }}', '{{ $valName }}')
                          }"
                          :disabled="!isOptionAvailable('{{ $attr['name'] }}', '{{ $valName }}')"
                          title="{{ $valName }}"
                          class="w-8 h-8 rounded-full transition-transform cursor-pointer shadow-2xs relative flex items-center justify-center border border-black/10">
                          <span class="w-full h-full rounded-full" style="background-color: {{ $valColor }};"></span>
                          <span x-show="selected['{{ $attr['name'] }}'] === '{{ $valName }}'" class="absolute inset-0 flex items-center justify-center text-xs {{ $isLight ? 'text-neutral-900' : 'text-white' }}">
                            <i class="fa-solid fa-check text-[10px]"></i>
                          </span>
                        </button>
                      @endforeach
                    </div>

                  @else
                    {{-- Kiểu hiển thị 3: Nút bấm chọn (Button / Label) --}}
                    <div class="flex flex-wrap gap-2">
                      @foreach($attr['values'] as $val)
                        <button type="button"
                          @click="selectAttribute('{{ $attr['name'] }}', '{{ $val }}')"
                          :class="{
                            'bg-[#051650] text-white border-[#051650] ring-2 ring-[#051650]/20 font-bold shadow-xs': selected['{{ $attr['name'] }}'] === '{{ $val }}',
                            'bg-white text-neutral-800 border-neutral-300 hover:border-[#051650] hover:text-[#051650]': selected['{{ $attr['name'] }}'] !== '{{ $val }}' && isOptionAvailable('{{ $attr['name'] }}', '{{ $val }}'),
                            'bg-neutral-100 text-neutral-400 border-neutral-200 line-through cursor-not-allowed opacity-50': !isOptionAvailable('{{ $attr['name'] }}', '{{ $val }}')
                          }"
                          :disabled="!isOptionAvailable('{{ $attr['name'] }}', '{{ $val }}')"
                          class="px-3.5 py-1.5 rounded-lg border text-xs sm:text-sm font-medium transition-all cursor-pointer select-none">
                          {{ $val }}
                        </button>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach

              {{-- Notice when invalid combination selected --}}
              <div x-show="isComplete && !matchedVariation" x-cloak class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-amber-600 shrink-0"></i>
                <span>Tổ hợp thuộc tính đã chọn hiện không khả dụng. Quý khách vui lòng chọn tùy chọn khác.</span>
              </div>
            </div>
          @endif

          <div class="pt-1">
            <div class="text-xs font-semibold uppercase tracking-wider text-[#818181] pb-2 border-b border-neutral-200">
              THÔNG TIN SẢN PHẨM
            </div>
            <div class="divide-y divide-neutral-200 text-xs sm:text-sm">
              <div class="py-3 flex items-start gap-4">
                <span class="w-28 sm:w-32 text-[#818181] shrink-0 font-normal">Mã sản phẩm</span>
                <span class="text-neutral-900 font-medium" x-text="currentSku">{{ $productCode }}</span>
              </div>
              <div class="py-3 flex items-center gap-4">
                <span class="w-28 sm:w-32 text-[#818181] shrink-0 font-normal">Thương hiệu</span>
                <div class="flex items-center gap-2">
                  @if($brandLogo)
                    <img class="h-6 w-auto object-contain" src="{{ $brandLogo }}" alt="{{ $brandName }}">
                  @endif
                  <span class="text-xs font-bold text-[#051650]">{{ $brandName }}</span>
                </div>
              </div>

              {{-- Descriptive Attributes (Thuộc tính mô tả - không tạo biến thể) --}}
              @if($descriptiveAttrs->isNotEmpty())
                @foreach($descriptiveAttrs as $dAttr)
                  <div class="py-3 flex items-start gap-4">
                    <span class="w-28 sm:w-32 text-[#818181] shrink-0 font-normal">{{ $dAttr['name'] }}</span>
                    <span class="text-neutral-900 font-medium">{{ is_array($dAttr['values']) ? implode(', ', $dAttr['values']) : $dAttr['values'] }}</span>
                  </div>
                @endforeach
              @endif

              @if(!empty($meta['voltage']))
                <div class="py-3 flex items-start gap-4">
                  <span class="w-28 sm:w-32 text-[#818181] shrink-0 font-normal">Cấp điện áp</span>
                  <span class="text-neutral-900 font-medium">{{ $meta['voltage'] }}</span>
                </div>
              @endif
              @if(!empty($meta['standard']))
                <div class="py-3 flex items-start gap-4">
                  <span class="w-28 sm:w-32 text-[#818181] shrink-0 font-normal">Tiêu chuẩn</span>
                  <span class="text-neutral-900 font-medium">{{ $meta['standard'] }}</span>
                </div>
              @endif
              <div class="py-3 flex items-start gap-4">
                <span class="w-28 sm:w-32 text-[#818181] shrink-0 font-normal">Mô tả ngắn</span>
                <div class="text-neutral-700 leading-relaxed text-xs sm:text-sm prose prose-sm max-w-none">
                  {!! $product->excerpt ?: 'Thiết bị bảo vệ quá tải và ngắn mạch phân phối trung thế ngoài trời, kiểm định đạt chuẩn EVN, bảo hành chính hãng uy tín từ Đại Phúc 68.' !!}
                </div>
              </div>
            </div>
          </div>

          {{-- Action Toolbar --}}
          <div class="pt-2 flex items-center justify-between gap-3 sm:gap-4 flex-wrap">
            <div class="flex items-center gap-3 sm:gap-3.5 flex-wrap">
              <div class="flex items-center justify-between border border-[#051650] rounded-lg bg-white h-8 w-[110px] px-2.5 shrink-0 shadow-2xs">
                <button class="btn-qty-decrease w-5 h-full flex items-center justify-center text-[#051650] hover:opacity-70 transition-opacity focus:outline-none cursor-pointer" type="button" @click="decreaseQty" aria-label="Giảm số lượng">
                  <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none">
                    <path d="M10.5 6.5H1.5C1.22386 6.5 1 6.27614 1 6C1 5.72386 1.22386 5.5 1.5 5.5H10.5C10.7761 5.5 11 5.72386 11 6C11 6.27614 10.7761 6.5 10.5 6.5Z" fill="#051650"></path>
                  </svg>
                </button>
                <input class="input-qty-value w-8 text-center bg-transparent font-bold text-sm text-[#051650] focus:outline-none" type="number" x-model.number="quantity" value="1" min="1" max="1000" aria-label="Số lượng sản phẩm">
                <button class="btn-qty-increase w-5 h-full flex items-center justify-center text-[#051650] hover:opacity-70 transition-opacity focus:outline-none cursor-pointer" type="button" @click="increaseQty" aria-label="Tăng số lượng">
                  <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none">
                    <path d="M6 1C6.27614 1 6.5 1.22386 6.5 1.5V5.5H10.5C10.7761 5.5 11 5.72386 11 6C11 6.27614 10.7761 6.5 10.5 6.5H6.5V10.5C6.5 10.7761 6.27614 11 6 11C5.72386 11 5.5 10.7761 5.5 10.5V6.5H1.5C1.22386 6.5 1 6.27614 1 6C1 5.72386 1.22386 5.5 1.5 5.5H5.5V1.5C5.5 1.22386 5.72386 1 6 1Z" fill="#051650"></path>
                  </svg>
                </button>
              </div>

              <button id="btn-product-detail-quote"
                class="h-8 px-6 rounded-lg bg-[#051650] hover:bg-[#0c2370] text-white font-bold text-xs uppercase tracking-wider transition-colors flex items-center justify-center cursor-pointer shadow-xs shrink-0 disabled:opacity-50 disabled:cursor-not-allowed"
                type="button"
                @click="handleContactQuote"
                :disabled="isVariable && isComplete && !matchedVariation"
                data-open-quote-modal
                data-product-title="{{ $product->title }}"
                data-product-image="{{ $product->featured_image ?: asset('storage/media/products/product-cau-chi-tu-roi-vina-electric.png') }}"
                data-product-sku="{{ $productCode }}"
                data-product-price="{{ $displayPrice }}"
                data-product-quantity="1">
                LIÊN HỆ BÁO GIÁ
              </button>
            </div>
            
            {{-- Stock Status Indicator --}}
            <div class="flex items-center gap-1.5 font-medium text-sm shrink-0"
              :class="isOutOfStock ? 'text-red-600' : 'text-[#1DA538]'">
              <template x-if="!isOutOfStock">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none">
                  <path d="M7.71 9.29C7.32 8.9 6.68 8.9 6.29 9.29C5.9 9.68 5.9 10.32 6.29 10.71L9.29 13.71C9.68 14.1 10.32 14.1 10.71 13.71L17.71 5.71C18.1 5.32 18.1 4.68 17.71 4.29C17.32 3.9 16.68 3.9 16.29 4.29L10 11.54L7.71 9.29Z" fill="#1DA538"></path>
                  <path d="M19 9C19 9.55 18.55 10 18 10C17.45 10 17 9.55 17 9C17 5.13 13.87 2 10 2C6.13 2 3 5.13 3 9C3 12.87 6.13 16 10 16C11.64 16 13.14 15.43 14.33 14.47C14.75 14.13 15.38 14.19 15.72 14.61C16.06 15.03 16 15.66 15.58 16C14.05 17.24 12.11 18 10 18C5.03 18 1 13.97 1 9C1 4.03 5.03 0 10 0C14.97 0 19 4.03 19 9Z" fill="#1DA538"></path>
                </svg>
              </template>
              <template x-if="isOutOfStock">
                <i class="fa-solid fa-circle-xmark text-red-600 text-base shrink-0"></i>
              </template>
              <span x-text="stockMessage">Có sẵn hàng tại kho</span>
            </div>
          </div>

          <div class="pt-3 flex items-center gap-6 text-xs text-neutral-600 select-none">
            <button class="inline-flex items-center gap-1.5 hover:text-[#051650] transition-colors focus:outline-none cursor-pointer" type="button" onclick="showToast('Đã lưu vào danh sách yêu thích!')">
              <svg class="w-4 h-4 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <span>Yêu thích sản phẩm</span>
            </button>
            <a class="inline-flex items-center gap-1.5 hover:text-[#051650] transition-colors focus:outline-none cursor-pointer" href="{{ asset('themes/daiphuc68/assets/ho-so-nang-luc.pdf') }}" target="_blank">
              <svg class="w-4 h-4 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
              </svg>
              <span>Xem Hồ Sơ Năng Lực</span>
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- Guarantee Highlights (Dynamic Backend Managed) --}}
  @include('themes.daiphuc68.components.product-guarantees', ['product' => $product])

  {{-- Tabs Section --}}
  <section class="py-12 md:py-16 bg-[#F6F6F6] border-t border-neutral-200 flex justify-center">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-16">
      <div class="bg-white rounded-2xl border border-neutral-200/90 shadow-sm overflow-hidden">
        
        {{-- Tab Navs --}}
        <div class="flex items-center border-b border-neutral-200 bg-neutral-50 overflow-x-auto select-none">
          <button class="product-tab-nav active px-6 py-4 text-xs font-bold uppercase tracking-wider text-brand-navy border-b-2 border-brand-navy bg-white whitespace-nowrap transition-colors focus:outline-none cursor-pointer" type="button" data-tab-target="tab-description">
            1. Mô Tả, Đặc Tính Kỹ Thuật
          </button>
        
        </div>

        {{-- Tab Panes --}}
        <div class="p-6 sm:p-8 lg:p-10">
          
          {{-- Tab 1: Description --}}
          <div class="product-tab-pane active flex flex-col gap-6 text-neutral-700 text-sm leading-relaxed" id="tab-description">
            <div class="w-full flex flex-col gap-4">
              <h3 class="text-xl font-bold text-brand-navy uppercase">
                Tổng Quan {{ $product->title }}
              </h3>

              {{-- Dynamic Table of Contents (TOC) --}}
              @if(!empty($tocData) && !empty($tocData['enabled']))
                @include('themes.daiphuc68.components.table-of-contents', ['tocData' => $tocData])
              @endif

              @if(!empty($product->content))
                <div class="prose max-w-none text-neutral-700 ck-content page-main-content">
                  {!! function_exists('sanitize_editor_html') ? sanitize_editor_html($tocData['content'] ?? $product->content) : ($tocData['content'] ?? $product->content) !!}
                </div>
              @else
                <p><strong>{{ $product->title }}</strong> mang thương hiệu <strong>{{ $brandName }}</strong> là thiết bị đóng cắt và bảo vệ lưới điện hàng đầu, đạt các chứng nhận quốc tế và kiểm định khắt khe của Tập đoàn Điện lực Việt Nam (EVN).</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                  <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-200 flex flex-col gap-2">
                    <div class="font-bold text-brand-navy flex items-center gap-2">
                      <i class="fa-solid fa-shield-halved text-brand-navy"></i>
                      <span>Vật Liệu Cách Điện Bền Vững</span>
                    </div>
                    <p class="text-xs text-neutral-600 leading-relaxed">Kháng tia cực tím, chống bám bụi bẩn, không bị rạn nứt trong môi trường sương muối và khí hậu nhiệt đới ẩm Việt Nam.</p>
                  </div>
                  <div class="p-4 rounded-xl bg-neutral-50 border border-neutral-200 flex flex-col gap-2">
                    <div class="font-bold text-brand-navy flex items-center gap-2">
                      <i class="fa-solid fa-bolt-lightning text-amber-500"></i>
                      <span>Tiếp Điểm Đồng Mạ Bạc Dày</span>
                    </div>
                    <p class="text-xs text-neutral-600 leading-relaxed">Bề mặt tiếp xúc điện bằng đồng tinh khiết mạ bạc chống mài mòn và oxy hóa, đảm bảo điện trở tiếp xúc cực thấp và khả năng chịu dòng ngắn mạch lớn.</p>
                  </div>
                </div>
              @endif
            </div>
          </div>

       

        </div>
      </div>
    </div>
  </section>

  {{-- Viewed Products Slider --}}
  @php
    $displayViewedProducts = $viewedProducts ?? $relatedProducts ?? collect();
  @endphp
  @if($displayViewedProducts->isNotEmpty())
    <section class="py-10 md:py-14 bg-[#F6F6F6] border-t border-neutral-200 flex justify-center">
      <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-16">
        <section class="viewed-products-section w-full pt-10 pb-8 md:pt-14 md:pb-12 select-none flex flex-col gap-6 sm:gap-8 items-center" id="detail-viewed-products-slider">
          <h2 class="text-center text-lg sm:text-xl md:text-[22px] font-black uppercase text-black tracking-wider">
            SẢN PHẨM ĐÃ XEM
          </h2>
          <div class="swiper viewed-product-swiper overflow-hidden w-full" data-swiper-id="detail-viewed-products-slider">
            <div class="swiper-wrapper">
              @foreach($displayViewedProducts as $relProduct)
                <div class="swiper-slide h-auto">
                  @include('themes.daiphuc68.components.product-card', ['product' => $relProduct])
                </div>
              @endforeach
            </div>
          </div>
          <div class="pt-6 sm:pt-8 flex items-center justify-between gap-6 sm:gap-8">
            <div class="flex-1 h-[2px] bg-black/20 relative rounded-full overflow-hidden">
              <div class="viewed-products-slider-progress detail-viewed-products-slider-progress h-full bg-black rounded-full transition-all duration-300 origin-left" style="width: 33.33%;"></div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
              <button class="viewed-products-slider-prev detail-viewed-products-slider-prev w-8 h-8 rounded-full border border-neutral-300 bg-white hover:bg-neutral-100 text-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition-all shadow-xs focus:outline-none cursor-pointer" type="button" aria-label="Slide trước" title="Trước">
                <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              <button class="viewed-products-slider-next detail-viewed-products-slider-next w-8 h-8 rounded-full border border-neutral-300 bg-white hover:bg-neutral-100 text-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition-all shadow-xs focus:outline-none cursor-pointer" type="button" aria-label="Slide tiếp theo" title="Tiếp theo">
                <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
        </section>
      </div>
    </section>
  @endif
@endsection

@push('scripts')
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
  <script>
    window.productDetailBox = function(config) {
      return {
        isVariable: config.isVariable,
        variations: config.variations || [],
        variationAttributes: config.variationAttributes || [],
        defaultPrice: config.defaultPrice,
        defaultOldPrice: config.defaultOldPrice,
        defaultSku: config.defaultSku,
        defaultStockStatus: config.defaultStockStatus,
        defaultStockQuantity: config.defaultStockQuantity,
        defaultManageStock: config.defaultManageStock,

        selected: {},
        matchedVariation: null,
        isComplete: false,
        quantity: 1,

        init() {
          if (this.isVariable && this.variationAttributes.length > 0) {
            this.variationAttributes.forEach(attr => {
              this.selected[attr.name] = '';
            });

            // Auto-select first active in-stock variation
            if (this.variations.length > 0) {
              const firstInStock = this.variations.find(v => v.is_in_stock) || this.variations[0];
              if (firstInStock && firstInStock.attributes) {
                this.variationAttributes.forEach(attr => {
                  if (firstInStock.attributes[attr.name]) {
                    this.selected[attr.name] = firstInStock.attributes[attr.name];
                  }
                });
                this.evaluateMatch();
              }
            }
          }
        },

        selectAttribute(name, val) {
          if (!val) return;
          this.selected[name] = val;

          // Check if current combination exists
          const keys = this.variationAttributes.map(a => a.name);
          const exactMatch = this.variations.find(v => {
            return keys.every(k => (v.attributes[k] || '').trim() === (this.selected[k] || '').trim());
          });

          // If current combination is incomplete or not found, find a compatible variation having this option
          if (!exactMatch) {
            const compatible = this.variations.find(v => (v.attributes[name] || '').trim() === val.trim());
            if (compatible && compatible.attributes) {
              this.variationAttributes.forEach(attr => {
                if (compatible.attributes[attr.name]) {
                  this.selected[attr.name] = compatible.attributes[attr.name];
                }
              });
            }
          }

          this.evaluateMatch();
        },

        evaluateMatch() {
          const keys = this.variationAttributes.map(a => a.name);
          this.isComplete = keys.every(k => !!this.selected[k]);

          if (!this.isComplete) {
            this.matchedVariation = null;
            return;
          }

          const match = this.variations.find(v => {
            return keys.every(k => (v.attributes[k] || '').trim() === (this.selected[k] || '').trim());
          });

          this.matchedVariation = match || null;

          if (this.matchedVariation && this.matchedVariation.image) {
            const activeSlideImg = document.querySelector('.product-gallery-main-swiper .swiper-slide-active img') || document.querySelector('.product-gallery-main-swiper img');
            if (activeSlideImg) {
              activeSlideImg.src = this.matchedVariation.image;
            }
          }
        },

        isOptionAvailable(name, val) {
          if (!this.isVariable || this.variations.length === 0) return true;
          return this.variations.some(v => (v.attributes[name] || '').trim() === val.trim());
        },

        get currentPrice() {
          if (!this.isVariable) return this.defaultPrice;
          if (this.matchedVariation) {
            return this.matchedVariation.formatted_price;
          }
          return this.defaultPrice;
        },

        get currentOldPrice() {
          if (!this.isVariable) return this.defaultOldPrice;
          if (this.matchedVariation) {
            return this.matchedVariation.formatted_regular_price || null;
          }
          return null;
        },

        get currentSku() {
          if (!this.isVariable) return this.defaultSku;
          if (this.matchedVariation && this.matchedVariation.sku) {
            return this.matchedVariation.sku;
          }
          return this.defaultSku;
        },

        get isOutOfStock() {
          if (!this.isVariable) {
            if (this.defaultManageStock) {
              return this.defaultStockQuantity <= 0;
            }
            return this.defaultStockStatus === 'outofstock';
          }
          if (this.matchedVariation) {
            return !this.matchedVariation.is_in_stock;
          }
          return false;
        },

        get stockMessage() {
          if (!this.isVariable) {
            if (this.defaultManageStock) {
              return this.defaultStockQuantity > 0 
                ? `Còn hàng (${this.defaultStockQuantity} sản phẩm có sẵn)`
                : 'Hết hàng tạm thời';
            }
            return this.defaultStockStatus === 'outofstock' ? 'Hết hàng' : 'Có sẵn hàng tại kho';
          }

          if (!this.isComplete) {
            return 'Tùy chọn theo biến thể';
          }

          if (!this.matchedVariation) {
            return 'Phiên bản không có sẵn';
          }

          if (this.matchedVariation.manage_stock) {
            return this.matchedVariation.stock_quantity > 0
              ? `Còn hàng (${this.matchedVariation.stock_quantity} sản phẩm có sẵn)`
              : 'Hết hàng tạm thời';
          }

          return this.matchedVariation.stock_status === 'outofstock' ? 'Hết hàng' : 'Có sẵn hàng tại kho';
        },

        decreaseQty() {
          if (this.quantity > 1) this.quantity--;
        },

        increaseQty() {
          if (this.isVariable && this.matchedVariation && this.matchedVariation.manage_stock) {
            if (this.quantity < this.matchedVariation.stock_quantity) {
              this.quantity++;
            } else {
              if (typeof window.showToast === 'function') {
                window.showToast(`Chỉ còn ${this.matchedVariation.stock_quantity} sản phẩm có sẵn trong kho.`, 'error');
              }
            }
          } else {
            this.quantity++;
          }
        },

        handleContactQuote() {
          if (this.isVariable && !this.isComplete) {
            if (typeof window.showToast === 'function') {
              window.showToast('Vui lòng chọn đầy đủ các tùy chọn biến thể!', 'error');
            }
            return;
          }
          if (this.isVariable && !this.matchedVariation) {
            if (typeof window.showToast === 'function') {
              window.showToast('Tùy chọn đã chọn hiện không khả dụng. Vui lòng chọn tổ hợp khác.', 'error');
            }
            return;
          }

          let variationDesc = '';
          if (this.isVariable && this.matchedVariation) {
            variationDesc = Object.entries(this.selected).map(([k, v]) => `${k}: ${v}`).join(' | ');
          }

          // Active product image
          let activeImg = '{{ $product->featured_image ?: asset("storage/media/products/product-cau-chi-tu-roi-vina-electric.png") }}';
          if (this.matchedVariation && this.matchedVariation.image) {
            activeImg = this.matchedVariation.image;
          } else {
            const slideImg = document.querySelector('.product-gallery-main-swiper .swiper-slide-active img') || document.querySelector('.product-gallery-main-swiper img');
            if (slideImg && slideImg.src) activeImg = slideImg.src;
          }

          if (typeof window.openProductQuoteModal === 'function') {
            window.openProductQuoteModal({
              title: '{{ $product->title }}',
              image: activeImg,
              sku: this.currentSku,
              variationText: variationDesc,
              price: this.currentPrice,
              quantity: this.quantity,
            });
          } else {
            const modal = document.getElementById('contact-modal-overlay');
            if (modal) {
              modal.classList.add('active');
              document.body.classList.add('modal-open');
              document.body.style.overflow = 'hidden';
            }
          }
        }
      };
    };

    // Handle Product Detail Tabs, Quote Modal & Quantity Buttons
    document.addEventListener('DOMContentLoaded', function () {
      // 1. Quote Modal Trigger for LIÊN HỆ BÁO GIÁ
      const quoteBtn = document.getElementById('btn-product-detail-quote');
      if (quoteBtn) {
        quoteBtn.addEventListener('click', function (e) {
          // If Alpine is active and product is variable, let Alpine handle it
          const alpineEl = document.querySelector('[x-data]');
          if (window.Alpine && alpineEl && alpineEl._x_dataStack && alpineEl._x_dataStack[0]) {
            const alpineData = alpineEl._x_dataStack[0];
            if (typeof alpineData.handleContactQuote === 'function') {
              alpineData.handleContactQuote();
              return;
            }
          }

          // Fallback direct execution
          const qtyInput = document.querySelector('.input-qty-value') || document.querySelector('input[x-model*="quantity"]');
          const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
          const currentSku = document.querySelector('[x-text="currentSku"]')?.textContent?.trim() || quoteBtn.getAttribute('data-product-sku') || '{{ $productCode }}';
          
          if (typeof window.openProductQuoteModal === 'function') {
            window.openProductQuoteModal({
              title: quoteBtn.getAttribute('data-product-title') || '{{ addslashes($product->title) }}',
              image: quoteBtn.getAttribute('data-product-image') || '',
              sku: currentSku,
              price: quoteBtn.getAttribute('data-product-price') || '{{ $displayPrice }}',
              quantity: qty,
              service: 'bao-gia-san-pham'
            });
          } else {
            const modal = document.getElementById('contact-modal-overlay');
            if (modal) {
              modal.classList.add('active');
              document.body.classList.add('modal-open');
              document.body.style.overflow = 'hidden';
            }
          }
        });
      }

      // 2. Quantity adjustment fallback
      const qtyInput = document.querySelector('.input-qty-value');
      const btnDec = document.querySelector('.btn-qty-decrease');
      const btnInc = document.querySelector('.btn-qty-increase');
      if (btnDec && qtyInput) {
        btnDec.addEventListener('click', function() {
          if (!window.Alpine) {
            let v = parseInt(qtyInput.value) || 1;
            if (v > 1) {
              qtyInput.value = v - 1;
              if (quoteBtn) quoteBtn.setAttribute('data-product-quantity', v - 1);
            }
          }
        });
      }
      if (btnInc && qtyInput) {
        btnInc.addEventListener('click', function() {
          if (!window.Alpine) {
            let v = parseInt(qtyInput.value) || 1;
            qtyInput.value = v + 1;
            if (quoteBtn) quoteBtn.setAttribute('data-product-quantity', v + 1);
          }
        });
      }

      // 3. Tab navigation
      const tabBtns = document.querySelectorAll('.product-tab-nav');
      const tabPanes = document.querySelectorAll('.product-tab-pane');

      tabBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          const targetId = this.getAttribute('data-tab-target');
          if (!targetId) return;

          tabBtns.forEach(b => {
            b.classList.remove('active', 'border-brand-navy', 'text-brand-navy', 'bg-white');
            b.classList.add('border-transparent', 'text-neutral-500');
          });

          this.classList.add('active', 'border-brand-navy', 'text-brand-navy', 'bg-white');
          this.classList.remove('border-transparent', 'text-neutral-500');

          tabPanes.forEach(pane => {
            if (pane.id === targetId) {
              pane.classList.remove('hidden');
              pane.classList.add('active');
            } else {
              pane.classList.add('hidden');
              pane.classList.remove('active');
            }
          });
        });
      });
    });
  </script>
@endpush
