@props([
    'product',
    'showQuickView' => true,
    'showWishlist' => true,
    'showCompare' => true,
    'layout' => 'horizontal', // horizontal, vertical, grid
    'size' => 'md'
])

@php
    $hasStock = $product->stock > 0;
    $hasContactPrice = $product->has_contact_price ?? false;
@endphp

<div {{ $attributes->merge(['class' => 'product-action-buttons']) }}>
    @if($layout === 'horizontal')
        <div class="flex items-center gap-2">
            {{-- Add to Cart / Contact Button --}}
            @if($hasStock && !$hasContactPrice)
                <x-ui.button 
                    variant="primary" 
                    :size="$size"
                    icon="fa-cart-shopping"
                    onclick="window.cart.add({{ $product->id }}, this)"
                    class="flex-1">
                    {{ __f('product_add_to_cart') }}
                </x-ui.button>
            @elseif($hasContactPrice)
                <x-ui.button 
                    variant="outline" 
                    :size="$size"
                    icon="fa-phone"
                    href="tel:{{ setting('site_phone') }}"
                    class="flex-1">
                    {{ __f('product_contact_price') }}
                </x-ui.button>
            @else
                <x-ui.button 
                    variant="secondary" 
                    :size="$size"
                    disabled
                    class="flex-1">
                    {{ __f('product_out_of_stock') }}
                </x-ui.button>
            @endif
            
            {{-- Quick Actions --}}
            <div class="flex items-center gap-1">
                @if($showWishlist)
                    <x-ui.button 
                        variant="ghost" 
                        :size="$size"
                        icon="fa-heart"
                        onclick="window.wishlist.toggle({{ $product->id }}, this)"
                        title="{{ __f('product_add_to_wishlist') }}"
                        class="aspect-square">
                    </x-ui.button>
                @endif
                
                @if($showCompare)
                    <x-ui.button 
                        variant="ghost" 
                        :size="$size"
                        icon="fa-balance-scale"
                        onclick="window.compare.toggle({{ $product->id }}, this)"
                        title="{{ __f('product_add_to_compare') }}"
                        class="aspect-square">
                    </x-ui.button>
                @endif
                
                @if($showQuickView)
                    <x-ui.button 
                        variant="ghost" 
                        :size="$size"
                        icon="fa-eye"
                        onclick="window.quickView.show({{ $product->id }})"
                        title="{{ __f('product_quick_view') }}"
                        class="aspect-square">
                    </x-ui.button>
                @endif
            </div>
        </div>
    @elseif($layout === 'vertical')
        <div class="flex flex-col gap-2">
            {{-- Add to Cart / Contact Button --}}
            @if($hasStock && !$hasContactPrice)
                <x-ui.button 
                    variant="primary" 
                    :size="$size"
                    icon="fa-cart-shopping"
                    onclick="window.cart.add({{ $product->id }}, this)"
                    fullWidth>
                    {{ __f('product_add_to_cart') }}
                </x-ui.button>
            @elseif($hasContactPrice)
                <x-ui.button 
                    variant="outline" 
                    :size="$size"
                    icon="fa-phone"
                    href="tel:{{ setting('site_phone') }}"
                    fullWidth>
                    {{ __f('product_contact_price') }}
                </x-ui.button>
            @else
                <x-ui.button 
                    variant="secondary" 
                    :size="$size"
                    disabled
                    fullWidth>
                    {{ __f('product_out_of_stock') }}
                </x-ui.button>
            @endif
            
            {{-- Quick Actions Row --}}
            <div class="flex items-center gap-1">
                @if($showWishlist)
                    <x-ui.button 
                        variant="ghost" 
                        :size="$size"
                        icon="fa-heart"
                        onclick="window.wishlist.toggle({{ $product->id }}, this)"
                        title="{{ __f('product_add_to_wishlist') }}"
                        class="flex-1">
                    </x-ui.button>
                @endif
                
                @if($showCompare)
                    <x-ui.button 
                        variant="ghost" 
                        :size="$size"
                        icon="fa-balance-scale"
                        onclick="window.compare.toggle({{ $product->id }}, this)"
                        title="{{ __f('product_add_to_compare') }}"
                        class="flex-1">
                    </x-ui.button>
                @endif
                
                @if($showQuickView)
                    <x-ui.button 
                        variant="ghost" 
                        :size="$size"
                        icon="fa-eye"
                        onclick="window.quickView.show({{ $product->id }})"
                        title="{{ __f('product_quick_view') }}"
                        class="flex-1">
                    </x-ui.button>
                @endif
            </div>
        </div>
    @else
        {{-- Grid layout - icon only buttons --}}
        <div class="grid grid-cols-2 gap-1">
            @if($hasStock && !$hasContactPrice)
                <x-ui.button 
                    variant="primary" 
                    :size="$size"
                    icon="fa-cart-shopping"
                    onclick="window.cart.add({{ $product->id }}, this)"
                    title="{{ __f('product_add_to_cart') }}"
                    class="col-span-2">
                </x-ui.button>
            @elseif($hasContactPrice)
                <x-ui.button 
                    variant="outline" 
                    :size="$size"
                    icon="fa-phone"
                    href="tel:{{ setting('site_phone') }}"
                    title="{{ __f('product_contact_price') }}"
                    class="col-span-2">
                </x-ui.button>
            @else
                <x-ui.button 
                    variant="secondary" 
                    :size="$size"
                    icon="fa-times"
                    disabled
                    title="{{ __f('product_out_of_stock') }}"
                    class="col-span-2">
                </x-ui.button>
            @endif
            
            @if($showWishlist)
                <x-ui.button 
                    variant="ghost" 
                    :size="$size"
                    icon="fa-heart"
                    onclick="window.wishlist.toggle({{ $product->id }}, this)"
                    title="{{ __f('product_add_to_wishlist') }}">
                </x-ui.button>
            @endif
            
            @if($showCompare)
                <x-ui.button 
                    variant="ghost" 
                    :size="$size"
                    icon="fa-balance-scale"
                    onclick="window.compare.toggle({{ $product->id }}, this)"
                    title="{{ __f('product_add_to_compare') }}">
                </x-ui.button>
            @endif
            
            @if($showQuickView)
                <x-ui.button 
                    variant="ghost" 
                    :size="$size"
                    icon="fa-eye"
                    onclick="window.quickView.show({{ $product->id }})"
                    title="{{ __f('product_quick_view') }}"
                    class="col-span-full">
                </x-ui.button>
            @endif
        </div>
    @endif
</div>