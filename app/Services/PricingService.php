<?php

namespace App\Services;

use App\Models\Product;

class PricingService
{
    private FlashSaleService $flashSaleService;

    public function __construct(FlashSaleService $flashSaleService)
    {
        $this->flashSaleService = $flashSaleService;
    }

    /**
     * Tính giá cuối cùng cho sản phẩm
     * SINGLE SOURCE OF TRUTH cho toàn bộ hệ thống
     */
    public function calculatePrice(Product $product, mixed $variant = null, mixed $mainProduct = null): array
    {
        $basePrice = $variant?->price ?? $product->price;
        $salePrice = $variant?->sale_price ?? $product->sale_price;
        $finalPrice = (float) $basePrice;
        $originalPrice = (float) $basePrice;
        $discountType = null;
        $discountValue = 0;

        // Ưu tiên 1: Flash Sale (cao nhất)
        $flashItem = $this->flashSaleService->getActiveItemForProduct($product);
        if ($flashItem) {
            $finalPrice = $flashItem->calcFlashPrice($originalPrice);
            $discountType = 'flash_sale';
            $discountValue = $originalPrice - $finalPrice;
        } elseif ($salePrice > 0 && $salePrice < $originalPrice) {
            $finalPrice = (float) $salePrice;
            $discountType = 'sale';
            $discountValue = $originalPrice - $finalPrice;
        }

        return [
            'original_price' => $originalPrice,
            'final_price' => $finalPrice,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'savings' => $originalPrice - $finalPrice,
            'discount_percent' => $originalPrice > 0 ? round((($originalPrice - $finalPrice) / $originalPrice) * 100) : 0,
            'is_combo' => false,
        ];
    }

    /**
     * Tính tổng giá trị giỏ hàng
     */
    public function calculateCartTotal(array $cart): array
    {
        $subtotal = 0;
        $totalSavings = 0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $itemTotal = ($item['price'] ?? 0) * ($item['qty'] ?? 1);
            $subtotal += $itemTotal;
            $itemCount += $item['qty'] ?? 1;

            // Tính tiết kiệm nếu có thông tin giá gốc
            if (isset($item['original_price']) && $item['original_price'] > $item['price']) {
                $itemSavings = ($item['original_price'] - $item['price']) * ($item['qty'] ?? 1);
                $totalSavings += $itemSavings;
            }
        }

        return [
            'subtotal' => $subtotal,
            'total_savings' => $totalSavings,
            'item_count' => $itemCount,
            'average_discount_percent' => $subtotal > 0 ? round(($totalSavings / ($subtotal + $totalSavings)) * 100) : 0,
        ];
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculateShippingFee(float $subtotal): float
    {
        $threshold = (float) setting('free_shipping_threshold', 500000);
        $defaultFee = (float) setting('default_shipping_fee', 30000);

        return $subtotal >= $threshold ? 0 : $defaultFee;
    }

    /**
     * Tính tổng đơn hàng cuối cùng
     */
    public function calculateOrderTotal(float $subtotal, float $discount = 0, float $shippingFee = 0): float
    {
        return max(0, $subtotal - $discount + $shippingFee);
    }
}
