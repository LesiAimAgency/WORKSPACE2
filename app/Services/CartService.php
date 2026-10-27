<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class CartService
{
    private PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(int $productId, ?int $variantId = null, int $qty = 1, ?int $mainProductId = null): array
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::find($variantId) : null;
        $mainProduct = $mainProductId ? Product::find($mainProductId) : null;

        // Validate stock
        $this->validateStock($product, $variant, $qty);

        // Calculate pricing
        $pricing = $this->pricingService->calculatePrice($product, $variant, $mainProduct);

        // Get cart from session
        $cart = session('cart', []);

        // Generate cart key
        $key = $this->generateCartKey($product, $variant, $pricing['is_combo']);

        // Add or update cart item
        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'id' => $product->id,
                'variant_id' => $variant?->id,
                'name' => $product->name,
                'variant_label' => $variant?->label,
                'price' => $pricing['final_price'],
                'original_price' => $pricing['original_price'],
                'image' => $this->normalizeImagePath($variant?->image ?? $product->image),
                'slug' => $product->slug,
                'qty' => $qty,
                'sku' => $variant?->sku ?? $product->sku,
                'is_combo' => $pricing['is_combo'],
                'discount_type' => $pricing['discount_type'],
                'savings' => $pricing['savings'],
            ];
        }

        // Save to session
        session(['cart' => $cart]);

        return [
            'success' => true,
            'cart' => $cart,
            'count' => array_sum(array_column($cart, 'qty')),
            'totals' => $this->pricingService->calculateCartTotal($cart),
        ];
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function updateQuantity(string $cartKey, int $qty): array
    {
        $cart = session('cart', []);

        if (! isset($cart[$cartKey])) {
            throw new \Exception('Sản phẩm không tồn tại trong giỏ hàng.');
        }

        if ($qty <= 0) {
            unset($cart[$cartKey]);
        } else {
            // Validate stock
            $product = Product::find($cart[$cartKey]['id']);
            $variant = $cart[$cartKey]['variant_id'] ? ProductVariant::find($cart[$cartKey]['variant_id']) : null;
            $this->validateStock($product, $variant, $qty);

            $cart[$cartKey]['qty'] = $qty;
        }

        session(['cart' => $cart]);

        return [
            'success' => true,
            'cart' => $cart,
            'count' => array_sum(array_column($cart, 'qty')),
            'totals' => $this->pricingService->calculateCartTotal($cart),
        ];
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart(string $cartKey): array
    {
        $cart = session('cart', []);
        unset($cart[$cartKey]);
        session(['cart' => $cart]);

        return [
            'success' => true,
            'cart' => $cart,
            'count' => array_sum(array_column($cart, 'qty')),
            'totals' => $this->pricingService->calculateCartTotal($cart),
        ];
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart(): array
    {
        session()->forget('cart');

        return [
            'success' => true,
            'cart' => [],
            'count' => 0,
            'totals' => $this->pricingService->calculateCartTotal([]),
        ];
    }

    /**
     * Lấy thông tin giỏ hàng
     */
    public function getCart(): array
    {
        $cart = session('cart', []);

        return [
            'cart' => $cart,
            'count' => array_sum(array_column($cart, 'qty')),
            'totals' => $this->pricingService->calculateCartTotal($cart),
        ];
    }

    /**
     * Validate stock availability
     */
    private function validateStock(Product $product, ?ProductVariant $variant, int $qty): void
    {
        if ($variant) {
            if ($variant->stock < $qty) {
                throw new \Exception("Phiên bản '{$variant->label}' không đủ số lượng trong kho!");
            }
        } else {
            if ($product->stock < $qty) {
                throw new \Exception("Sản phẩm '{$product->name}' không đủ số lượng trong kho!");
            }
        }
    }

    /**
     * Generate unique cart key
     */
    private function generateCartKey(Product $product, ?ProductVariant $variant, bool $isCombo): string
    {
        $key = $variant ? ($product->id.'-'.$variant->id) : (string) $product->id;

        if ($isCombo) {
            $key .= '-combo';
        }

        return $key;
    }

    /**
     * Normalize image path
     */
    private function normalizeImagePath(?string $image): ?string
    {
        if (! $image) {
            return null;
        }
        if (str_starts_with($image, 'http')) {
            return $image;
        }

        $path = ltrim($image, '/');
        if (str_starts_with($path, 'media/')) {
            return \Storage::disk('public')->url($path);
        }

        return asset($path);
    }
}
