@props([
    'product',
    'showQuantity' => true,
    'showBuyNow' => false
])

@php
    $hasStock = $product->stock > 0;
    $hasContactPrice = $product->has_contact_price ?? false;
    $hotline = setting('contact_phone') ?: (setting('site_phone') ?: '0901 234 567');
    $cleanHotline = preg_replace('/[^0-9+]/', '', $hotline);
@endphp

<div {{ $attributes->merge(['class' => 'product-detail-actions mb--25']) }}>
    @if($hasStock && !$hasContactPrice)
        {{-- In Stock Actions --}}
        <div class="d-flex align-items-center flex-wrap gap-3">
            @if($showQuantity)
                {{-- Quantity Selector --}}
                <div class="cart-edits">
                    <div class="quantity-edit action-item" style="border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; height: 48px; display: inline-flex; align-items: center; padding: 2px;">
                        <button type="button" class="button" @click.stop.prevent="if(qty > 1) qty = qty - 1" style="width: 36px; height: 100%; border: none; background: transparent; cursor: pointer; color: #475569;"><i class="fal fa-minus minus"></i></button>
                        <input type="text" class="input" x-model="qty" @input="qty = Math.max(1, Math.min({{ $product->stock }}, parseInt($event.target.value) || 1))" style="width: 48px; height: 100%; text-align: center; border: none; background: transparent; font-weight: 600; color: #1e293b; font-size: 15px;">
                        <button type="button" class="button plus" @click.stop.prevent="if(qty < {{ $product->stock }}) qty = qty + 1" :disabled="qty >= {{ $product->stock }}" style="width: 36px; height: 100%; border: none; background: transparent; cursor: pointer; color: #475569;"><i class="fal fa-plus plus"></i></button>
                    </div>
                </div>
            @endif

            {{-- Add to Cart Button --}}
            <a href="javascript:void(0);" @click="window.cart.add({{ $product->id }}, $event.target, qty)" class="rts-btn btn-primary radious-sm with-icon">
                <div class="btn-text">
                    {{ __('product_add_to_cart') }}
                </div>
                <div class="arrow-icon">
                    <i class="fa-regular fa-cart-shopping"></i>
                </div>
                <div class="arrow-icon">
                    <i class="fa-regular fa-cart-shopping"></i>
                </div>
            </a>

            @if($showBuyNow)
                {{-- Buy Now Button --}}
                <button type="button" onclick="buyNow({{ $product->id }}, qty)" class="rts-btn btn-secondary radious-sm" style="height: 48px; display: inline-flex; align-items: center; padding: 0 24px; border-radius: 8px; font-weight: 600; font-size: 15px;">
                    <i class="fa-solid fa-bolt mr-2" style="margin-right: 8px;"></i>
                    {{ __('product_buy_now') }}
                </button>
            @endif

            {{-- Wishlist Button --}}
            <button type="button" onclick="cwAction.addWishlist({{ $product->id }}, this)" class="btn d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px; max-height: 48px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #e11d48; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" title="{{ __('Yêu thích') }}">
                <i class="fa-light fa-heart" style="font-size: 20px;"></i>
            </button>
        </div>

    @elseif($hasContactPrice)
        {{-- Contact for Price --}}
        <div class="d-flex align-items-center flex-wrap gap-3">
            <a href="tel:{{ $cleanHotline }}" class="rts-btn btn-primary radious-sm with-icon" style="height: 48px; display: inline-flex; align-items: center; padding: 0 24px; border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-phone-volume mr-2" style="margin-right: 10px;"></i>
                {{ __('product_contact_price') }}: {{ $hotline }}
            </a>
            <button type="button" onclick="cwAction.addWishlist({{ $product->id }}, this)" class="btn d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px; max-height: 48px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #e11d48;" title="{{ __('Yêu thích') }}">
                <i class="fa-light fa-heart" style="font-size: 20px;"></i>
            </button>
        </div>

    @else
        {{-- Out of Stock Notice (Premium Redesigned Card) --}}
        <div class="vtm-out-of-stock-box p-4 rounded-3 mb-3" style="background: linear-gradient(135deg, #fffafa 0%, #fff5f5 100%); border: 1px solid #fecaca; border-radius: 12px; box-shadow: 0 2px 10px rgba(220, 38, 38, 0.04);">
            <div class="d-flex align-items-start gap-3">
                <div class="vtm-oos-badge d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; border-radius: 10px; background: #fee2e2; color: #dc2626; font-size: 20px;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                        <h4 class="m-0 font-bold" style="color: #991b1b; font-size: 17px; font-weight: 700;">{{ __('product_temporarily_out') }}</h4>
                        <span class="badge" style="background: #fee2e2; color: #dc2626; font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600; letter-spacing: 0.3px;">TẠM HẾT HÀNG</span>
                    </div>
                    <p class="m-0" style="font-size: 13.5px; line-height: 1.5; color: #7f1d1d;">
                        {{ __('product_contact_similar') }}
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mt-3 pt-3 flex-wrap" style="border-top: 1px dashed #fca5a5;">
                <a href="tel:{{ $cleanHotline }}" class="btn d-inline-flex align-items-center gap-2 px-3 py-2 text-white font-medium" style="background: #dc2626; border: none; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; box-shadow: 0 2px 6px rgba(220,38,38,0.25);">
                    <i class="fa-solid fa-phone-volume"></i>
                    <span>{{ __('product_call_now') }}: <strong>{{ $hotline }}</strong></span>
                </a>

                <a href="https://zalo.me/{{ $cleanHotline }}" target="_blank" rel="noopener" class="btn d-inline-flex align-items-center gap-2 px-3 py-2 font-medium" style="border: 1px solid #2563eb; color: #2563eb; background: #eff6ff; border-radius: 8px; font-size: 13.5px; font-weight: 500; text-decoration: none;">
                    <i class="fa-solid fa-headset"></i>
                    <span>Tư vấn trực tuyến</span>
                </a>

                {{-- Wishlist Button --}}
                <button type="button" onclick="cwAction.addWishlist({{ $product->id }}, this)" class="btn d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px; max-height: 40px; border-radius: 8px; border: 1px solid #fecaca; background: #fff; color: #e11d48; transition: all 0.2s;" title="{{ __('Thêm vào yêu thích') }}">
                    <i class="fa-light fa-heart" style="font-size: 17px;"></i>
                </button>
            </div>
        </div>
    @endif
</div>