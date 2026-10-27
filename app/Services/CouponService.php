<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * Apply coupon atomic - đảm bảo không vượt usage_limit
     */
    public function applyCoupon(int $couponId, float $orderTotal): array
    {
        return DB::transaction(function () use ($couponId, $orderTotal) {
            $coupon = Coupon::lockForUpdate()->find($couponId);

            if (! $coupon) {
                throw new \Exception('Mã giảm giá không tồn tại.');
            }

            // Kiểm tra tính hợp lệ
            $invalidReason = $coupon->getInvalidReason($orderTotal);
            if ($invalidReason) {
                throw new \Exception("Mã giảm giá không hợp lệ: {$invalidReason}");
            }

            // Kiểm tra usage_limit atomic
            if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
                throw new \Exception('Mã giảm giá đã hết lượt sử dụng.');
            }

            // Tính discount
            $discount = $coupon->calculateDiscount($orderTotal);

            // Tăng usage_count atomic
            $coupon->increment('usage_count');

            return [
                'coupon' => $coupon,
                'discount' => $discount,
                'code' => $coupon->code,
            ];
        });
    }

    /**
     * Apply multiple coupons atomic
     */
    public function applyMultipleCoupons(array $couponIds, float $orderTotal): array
    {
        return DB::transaction(function () use ($couponIds, $orderTotal) {
            $appliedCoupons = [];
            $totalDiscount = 0;
            $appliedCodes = [];

            foreach ($couponIds as $couponId) {
                try {
                    $result = $this->applyCouponWithoutTransaction($couponId, $orderTotal);
                    $appliedCoupons[] = $result;
                    $totalDiscount += $result['discount'];
                    $appliedCodes[] = $result['code'];
                } catch (\Exception $e) {
                    // Log lỗi nhưng không throw để các coupon khác vẫn được áp dụng
                    \Log::warning("Coupon {$couponId} failed: ".$e->getMessage());
                }
            }

            return [
                'applied_coupons' => $appliedCoupons,
                'total_discount' => $totalDiscount,
                'applied_codes' => $appliedCodes,
            ];
        });
    }

    /**
     * Apply coupon without transaction (for use inside existing transaction)
     */
    private function applyCouponWithoutTransaction(int $couponId, float $orderTotal): array
    {
        $coupon = Coupon::lockForUpdate()->find($couponId);

        if (! $coupon) {
            throw new \Exception('Mã giảm giá không tồn tại.');
        }

        // Kiểm tra tính hợp lệ
        $invalidReason = $coupon->getInvalidReason($orderTotal);
        if ($invalidReason) {
            throw new \Exception("Mã giảm giá không hợp lệ: {$invalidReason}");
        }

        // Kiểm tra usage_limit atomic
        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            throw new \Exception('Mã giảm giá đã hết lượt sử dụng.');
        }

        // Tính discount
        $discount = $coupon->calculateDiscount($orderTotal);

        // Tăng usage_count atomic
        $coupon->increment('usage_count');

        return [
            'coupon' => $coupon,
            'discount' => $discount,
            'code' => $coupon->code,
        ];
    }

    /**
     * Validate coupon trước khi apply (không thay đổi usage_count)
     */
    public function validateCoupon(string $code, float $orderTotal): array
    {
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();

        if (! $coupon) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá không tồn tại.',
            ];
        }

        $invalidReason = $coupon->getInvalidReason($orderTotal);
        if ($invalidReason) {
            return [
                'valid' => false,
                'message' => "Mã giảm giá không hợp lệ: {$invalidReason}",
            ];
        }

        $discount = $coupon->calculateDiscount($orderTotal);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Mã giảm giá hợp lệ. Giảm '.number_format($discount, 0, ',', '.').'₫',
        ];
    }
}
