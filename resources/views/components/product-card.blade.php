@props(['product'])

@php
    $thumbnailUrl = $product->thumbnail_url ?: asset('theme/images/grocery/01.jpg');
    $categoryName = $product->categories->first()->name ?? __f('nav_shop');
    // Ưu tiên flash sale discount, fallback về compare_price discount
    $discountPercent = $product->flash_discount_percent ?? $product->discount_percent;
    $isFlashSale = $product->flash_price !== null;

    $bestSellerBadge = setting('badge_best_seller_img');
    $featuredBadge = setting('badge_featured_img');
    $discountBadge = setting('badge_discount_img');

    $isHot = $product->is_best_seller;
    $isNew = $product->is_featured;
@endphp

<div class="single-shopping-card-one" x-data="{ qty: 1 }">
    <!-- image and action area start -->
    <div class="image-and-action-area-wrapper">
        <a href="{{ locale_route('shop.show', $product->slug) }}" class="thumbnail-preview">
            <div class="badge">
                @if($discountPercent > 0)
                    <span>-{{ $discountPercent }}% <br> {{ __f('badge_off') }}</span>
                @elseif($isNew)
                    <span>{{ __f('badge_new_arrive') }}</span>
                @elseif($isHot)
                    <span>{{ __f('badge_hot_sale') }}</span>
                @endif
                <i class="fa-solid fa-bookmark"></i>
            </div>
            <img src="{{ $thumbnailUrl }}" alt="{{ $product->name }}">
        </a>
        <div class="action-share-option">
            <div class="single-action openuptip message-show-action" data-flow="up" title="{{ __f('product_add_to_wishlist') }}"
                @click="window.cwAction.addWishlist({{ $product->id }}, $event.target)">
                <i class="fa-light fa-heart"></i>
            </div>
            <div class="single-action openuptip" data-flow="up" title="{{ __f('product_add_to_compare') }}"
                @click="window.cwAction.addCompare({{ $product->id }}, $event.target)">
                <i class="fa-solid fa-arrows-retweet"></i>
            </div>
            <div class="single-action openuptip cta-quickview product-details-popup-btn" data-flow="up"
                title="{{ __f('product_quick_view') }}" @click="window.cwAction.quickView({{ $product->id }})">
                <i class="fa-regular fa-eye"></i>
            </div>
        </div>
    </div>
    <!-- image and action area end -->
    <div class="body-content">

        <a href="{{ locale_route('shop.show', $product->slug) }}">
            <h4 class="title">{{ $product->name }}</h4>
        </a>
        <span class="availability" style="text-align: left;">{{ $product->unit ?? __f('product_unit_default') }}</span>
        <div class="price-area">
            <span class="current">{{ $product->formatted_price }}</span>
            @if($product->old_price > $product->effective_price)
                <div class="previous">{{ number_format($product->old_price, 0, ',', '.') }}đ</div>
            @endif
        </div>
        @if(!$product->has_contact_price)
            <div class="cart-counter-action">
                <div class="quantity-edit">
                    <input type="text" class="input" x-model="qty" >
                    <div class="button-wrapper-action">
                        <button class="button" @click="qty > 1 ? qty-- : 1"><i
                                class="fa-regular fa-chevron-down"></i></button>
                        <button class="button plus" @click="qty++"><i class="fa-regular fa-chevron-up"></i></button>
                    </div>
                </div>
                <a href="javascript:void(0);" @click="window.cart.add({{ $product->id }}, $event.target, qty)"
                    class="rts-btn btn-primary radious-sm with-icon">
                    <div class="btn-text">
                        {{ __f('product_add_to_cart') }}
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
                    {{ __f('product_call_now') }}
                </a>
            </div>
        @endif
    </div>
</div>