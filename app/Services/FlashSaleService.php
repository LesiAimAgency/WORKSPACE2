<?php

namespace App\Services;

use App\Models\FlashSaleCampaign;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Illuminate\Cache\RedisStore;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class FlashSaleService
{
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = FlashSaleCampaign::withCount('items')->latest();

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function find(int $id): ?FlashSaleCampaign
    {
        return FlashSaleCampaign::with(['items.product', 'items.category'])->find($id);
    }

    public function create(array $data): FlashSaleCampaign
    {
        $campaign = FlashSaleCampaign::create($this->prepareCampaignData($data));
        $this->syncItems($campaign, $data['items'] ?? []);

        return $campaign;
    }

    public function update(int $id, array $data): bool
    {
        $campaign = FlashSaleCampaign::findOrFail($id);
        $campaign->update($this->prepareCampaignData($data));
        $this->syncItems($campaign, $data['items'] ?? []);

        // Clear cache sau khi update
        $this->clearCache();

        return true;
    }

    public function delete(int $id): bool
    {
        $result = (bool) FlashSaleCampaign::destroy($id);

        // Clear cache sau khi delete
        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Lấy flash sale item đang chạy cho một sản phẩm (theo product_id hoặc category_id)
     * SỬ DỤNG CACHE để tránh query lặp lại
     */
    public function getActiveItemForProduct(Product $product): ?FlashSaleItem
    {
        $cacheKey = "flash_sale_item_product_{$product->id}";

        return \Cache::remember($cacheKey, 300, function () use ($product) { // Cache 5 phút
            $now = Carbon::now();

            // 1. Ưu tiên item theo sản phẩm cụ thể
            $item = FlashSaleItem::whereHas('campaign', function ($q) use ($now) {
                $q->where('status', 'active')
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now);
            })
                ->where('product_id', $product->id)
                ->first();

            if ($item) {
                return $item;
            }

            // 2. Fallback: item theo danh mục (kiểm tra tất cả danh mục của sản phẩm)
            $categoryIds = $product->categories->pluck('id')->toArray();
            if (! empty($categoryIds)) {
                $item = FlashSaleItem::whereHas('campaign', function ($q) use ($now) {
                    $q->where('status', 'active')
                        ->where('starts_at', '<=', $now)
                        ->where('ends_at', '>=', $now);
                })
                    ->whereIn('category_id', $categoryIds)
                    ->first();
            }

            if ($item) {
                return $item;
            }

            // 3. Fallback: legacy category_id if exists
            if (! empty($product->category_id)) {
                $item = FlashSaleItem::whereHas('campaign', function ($q) use ($now) {
                    $q->where('status', 'active')
                        ->where('starts_at', '<=', $now)
                        ->where('ends_at', '>=', $now);
                })
                    ->where('category_id', $product->category_id)
                    ->first();
            }

            return $item;
        });
    }

    /**
     * Lấy chiến dịch đang chạy hiện tại (dùng cho widget)
     * SỬ DỤNG CACHE
     */
    public function getRunningCampaign(): ?FlashSaleCampaign
    {
        return \Cache::remember('running_flash_campaign', 300, function () {
            return FlashSaleCampaign::running()->with('items')->latest('starts_at')->first();
        });
    }

    /**
     * Clear cache khi có thay đổi campaign
     */
    public function clearCache(): void
    {
        \Cache::forget('running_flash_campaign');
        // Clear cache cho tất cả sản phẩm (pattern-based clear nếu Redis)
        if (\Cache::getStore() instanceof RedisStore) {
            \Cache::getStore()->getRedis()->eval("return redis.call('del', unpack(redis.call('keys', ARGV[1])))", 0, 'flash_sale_item_product_*');
        }
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    private function prepareCampaignData(array $data): array
    {
        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'starts_at' => Carbon::parse($data['starts_at']),
            'ends_at' => Carbon::parse($data['ends_at']),
            'status' => $data['status'] ?? 'draft',
            'apply_to_all' => ! empty($data['apply_to_all']),
        ];
    }

    private function syncItems(FlashSaleCampaign $campaign, array $items): void
    {
        // Xóa items cũ
        $campaign->items()->delete();

        foreach ($items as $item) {
            if (empty($item['discount_value'])) {
                continue;
            }
            if (empty($item['product_id']) && empty($item['category_id'])) {
                continue;
            }

            FlashSaleItem::create([
                'campaign_id' => $campaign->id,
                'product_id' => ! empty($item['product_id']) ? $item['product_id'] : null,
                'category_id' => ! empty($item['category_id']) ? $item['category_id'] : null,
                'discount_type' => $item['discount_type'] ?? 'percent',
                'discount_value' => $item['discount_value'],
                'sale_limit' => ! empty($item['sale_limit']) ? $item['sale_limit'] : null,
            ]);
        }
    }
}
