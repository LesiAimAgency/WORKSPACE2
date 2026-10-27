@php
    $productMeta = is_object($product ?? null) 
        ? (is_string($product->meta_data ?? null) ? json_decode($product->meta_data, true) : ($product->meta_data ?? []))
        : (is_array($product ?? null) ? ($product['meta_data'] ?? $product) : []);
    
    $guaranteeOverride = $productMeta['guarantee_override'] ?? 'default';
    $customGuarantees = $productMeta['custom_guarantees'] ?? [];

    $globalSetting = setting('product_guarantees');
    if (is_string($globalSetting)) {
        $globalSetting = json_decode($globalSetting, true) ?: [];
    }

    $isEnabled = isset($globalSetting['enabled']) ? (bool)$globalSetting['enabled'] : true;

    // Default fallback items matching the theme design
    $defaultItems = [
        [
            'icon' => 'themes/daiphuc68/assets/icons/guarantee.svg',
            'title' => 'HÀNG CHẤT LƯỢNG CHÍNH HÃNG',
            'description' => 'Cam kết xuất xứ rõ ràng, đầy đủ chứng chỉ CO/CQ & tiêu chuẩn EVN.',
            'link' => '',
        ],
        [
            'icon' => 'themes/daiphuc68/assets/icons/rocket.svg',
            'title' => 'GIAO HÀNG THẦN TỐC TRONG VÒNG 24H',
            'description' => 'Giao hàng trong vòng 24h, hỗ trợ vận chuyển toàn quốc tận công trình.',
            'link' => '',
        ],
        [
            'icon' => 'themes/daiphuc68/assets/icons/preferences.svg',
            'title' => 'BẢO HÀNH DÀI HẠN LÊN ĐẾN 36 THÁNG',
            'description' => 'Bảo hành chính hãng lên đến 36 tháng, hỗ trợ kỹ thuật bảo trì tận tâm.',
            'link' => '',
        ],
    ];

    // Determine items to display based on product override
    if ($guaranteeOverride === 'hide') {
        $showSection = false;
        $items = [];
    } elseif ($guaranteeOverride === 'custom' && !empty($customGuarantees) && is_array($customGuarantees)) {
        $showSection = true;
        $items = $customGuarantees;
    } else {
        $showSection = $isEnabled;
        $savedItems = $globalSetting['items'] ?? null;
        $items = (!empty($savedItems) && is_array($savedItems)) ? $savedItems : $defaultItems;
    }

    $totalItems = count($items);
@endphp

@if($showSection && $totalItems > 0)
<section class="bg-white border-t border-b border-[#818181]/40 py-10 md:py-12 flex justify-center" id="product-guarantees-section">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-16">
        <div class="grid grid-cols-1 {{ $totalItems === 1 ? 'max-w-xl w-full justify-self-center' : ($totalItems === 2 ? 'md:grid-cols-2' : ($totalItems === 4 ? 'md:grid-cols-2 lg:grid-cols-4' : 'md:grid-cols-3')) }} gap-9 md:gap-4 items-center">
            @foreach($items as $index => $item)
                @php
                    $rawIcon = trim($item['icon'] ?? '');
                    $isFontAwesome = str_starts_with($rawIcon, 'fa-') || str_contains($rawIcon, ' fa-');
                    
                    if (!$isFontAwesome) {
                        if (empty($rawIcon)) {
                            $iconUrl = asset('themes/daiphuc68/assets/icons/guarantee.svg');
                        } elseif (str_starts_with($rawIcon, 'http://') || str_starts_with($rawIcon, 'https://')) {
                            $iconUrl = $rawIcon;
                        } elseif (str_starts_with($rawIcon, '/storage') || str_starts_with($rawIcon, 'storage/')) {
                            $iconUrl = asset(ltrim($rawIcon, '/'));
                        } else {
                            $iconUrl = asset(ltrim($rawIcon, '/'));
                        }
                    }

                    // Border styling for responsive column divider
                    if ($totalItems === 1) {
                        $borderClasses = 'justify-center text-center sm:text-left';
                    } elseif ($totalItems === 2) {
                        $borderClasses = $loop->first ? 'md:pr-8 md:border-r md:border-[#818181]/40' : 'md:pl-8';
                    } elseif ($totalItems === 3) {
                        $borderClasses = $loop->first ? 'md:pr-8' : ($loop->last ? 'md:pl-8' : 'md:px-8 md:border-x md:border-[#818181]/40');
                    } else {
                        $borderClasses = 'md:px-6 ' . (!$loop->last ? 'md:border-r md:border-[#818181]/40' : '');
                    }

                    $title = $item['title'] ?? '';
                    $description = $item['description'] ?? '';
                    $link = $item['link'] ?? '';
                @endphp
                <div class="flex items-center gap-5 {{ $borderClasses }}">
                    @if($isFontAwesome)
                        <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center shrink-0 text-[#051650]">
                            <i class="{{ $rawIcon }} text-4xl sm:text-5xl"></i>
                        </div>
                    @else
                        <img class="w-16 h-16 sm:w-20 sm:h-20 object-contain shrink-0" 
                             src="{{ $iconUrl }}" 
                             alt="{{ $title ?: 'Cam kết chất lượng' }}" 
                             onerror="this.onerror=null; this.src='{{ asset('themes/daiphuc68/assets/icons/rocket.svg') }}';">
                    @endif
                    <div class="flex flex-col gap-1 justify-start items-start">
                        @if(!empty($link))
                            <a href="{{ $link }}" class="group block" title="{{ $title }}">
                                <h4 class="text-base sm:text-lg lg:text-[20px] font-medium text-[#051650] group-hover:text-blue-600 transition-colors uppercase tracking-tight">
                                    {{ $title }}
                                </h4>
                            </a>
                        @else
                            <h4 class="text-base sm:text-lg lg:text-[20px] font-medium text-[#051650] uppercase tracking-tight">
                                {{ $title }}
                            </h4>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
