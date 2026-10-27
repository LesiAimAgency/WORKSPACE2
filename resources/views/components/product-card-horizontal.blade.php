@props(['product'])

@php
    $thumbnailUrl = $product->thumbnail_url ?: asset('theme/images/grocery/01.jpg');
    $discountPercent = $product->flash_discount_percent ?? $product->discount_percent;
    $isFlashSale = $product->flash_price !== null;
    $bestSellerBadge = setting('badge_best_seller_img');
    $featuredBadge = setting('badge_featured_img');
    $discountBadge = setting('badge_discount_img');
    $isHot = $product->is_best_seller;
    $isNew = $product->is_featured;
@endphp

<div class="single-shopping-card-one {{ $discountPercent > 0 ? 'discount-offer' : '' }}">
    <a href="{{ locale_route('shop.show', $product->slug) }}" class="thumbnail-preview">
        <div class="product-badge-wrapper">
            @if($isHot)
                @if($bestSellerBadge)
                    <div class="p-badge p-badge-img" style="background-image: url('{{ media_url($bestSellerBadge) }}')"><span>HOT</span></div>
                @else
                    <div class="p-badge p-badge-hot"><span>HOT</span></div>
                @endif
            @endif
            
            @if($isNew)
                @if($featuredBadge)
                    <div class="p-badge p-badge-img" style="background-image: url('{{ media_url($featuredBadge) }}')"><span>NEW</span></div>
                @else
                    <div class="p-badge p-badge-new"><span>NEW</span></div>
                @endif
            @endif

            @if($discountPercent > 0 || $isFlashSale)
                @if($discountBadge)
                    <div class="p-badge p-badge-img" style="background-image: url('{{ media_url($discountBadge) }}')"><span>-{{ $discountPercent }}%</span></div>
                @else
                    <div class="p-badge p-badge-discount"><span>-{{ $discountPercent }}%</span></div>
                @endif
            @endif
        </div>
        <img src="{{ $thumbnailUrl }}" alt="{{ $product->name }}">
    </a>
    <div class="body-content">

        <a href="{{ locale_route('shop.show', $product->slug) }}">
            <h4 class="title">{{ $product->name }}</h4>
        </a>
        <span class="availability" style="text-align: left;">{{ $product->unit ?? 'Gói' }}</span>
        <div class="price-area">
            <span class="current" @if($isFlashSale) style="color: var(--color-danger, #DC2626);" @endif>
                {{ $product->formatted_price }}
            </span>
            @if($product->old_price > $product->effective_price)
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div class="previous">{{ number_format($product->old_price, 0, ',', '.') }}đ</div>
                    @if($discountPercent > 0)
                        <span style="background: var(--color-danger); color: white; border-radius: 4px; padding: 2px 6px; font-size: 11px; font-weight: 700;">-{{ $discountPercent }}%</span>
                    @endif
                </div>
            @endif
        </div>
        @if(!$product->has_contact_price)
            <div class="cart-counter-action" x-data="{ qty: 1 }">
                <div class="quantity-edit">
                    <input type="text" class="input" x-model="qty" >
                    <div class="button-wrapper-action">
                        <button class="button" @click="qty > 1 ? qty-- : 1"><i class="fa-regular fa-chevron-down"></i></button>
                        <button class="button plus" @click="qty++"><i class="fa-regular fa-chevron-up"></i></button>
                    </div>
                </div>
                <a href="javascript:void(0);" @click="cart.add({{ $product->id }}, $event.target, qty)" class="rts-btn btn-primary radious-sm with-icon">
                    <div class="btn-text">
                        Thêm
                    </div>
                    <div class="arrow-icon">
                        <i class="fa-regular fa-cart-shopping"></i>
                    </div>
                    <div class="arrow-icon">
                        <i class="fa-regular fa-cart-shopping"></i>
                    </div>
                </a>
            </div>
        @else
            <div class="cart-counter-action">
                <a href="tel:{{ setting('site_phone') }}" class="rts-btn btn-primary radious-sm w-100 text-center">
                    Liên hệ ngay
                </a>
            </div>
        @endif
    </div>
</div>
