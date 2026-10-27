<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductAttributeValueMapping;
use Illuminate\Support\Collection;

/**
 * Service để quản lý Product Attributes
 *
 * Xử lý logic của cấu trúc 3 lớp:
 * 1. ProductAttribute - Loại thuộc tính (Color, Size)
 * 2. ProductAttributeValue - Giá trị (Red, Blue, M, L)
 * 3. ProductAttributeValueMapping - Kết nối product + values
 */
class ProductAttributeService
{
    /**
     * Gán attribute values cho sản phẩm
     *
     * Ví dụ:
     * $service->assignAttributes($product, [
     *     'color' => [1, 2],  // Color attribute: Red (1), Blue (2)
     *     'size' => [3, 4],   // Size attribute: M (3), L (4)
     * ])
     */
    public function assignAttributes(Product $product, array $attributeData): void
    {
        // Xóa mappings cũ
        $product->attributeMappings()->delete();

        // Tạo mappings mới
        foreach ($attributeData as $attributeIdOrSlug => $valueIds) {
            // Nếu là slug thì convert sang ID
            $attributeId = is_numeric($attributeIdOrSlug)
                ? $attributeIdOrSlug
                : ProductAttribute::where('slug', $attributeIdOrSlug)->value('id');

            if (! $attributeId) {
                continue;
            }

            // Tạo mapping cho mỗi value
            foreach ((array) $valueIds as $valueId) {
                ProductAttributeValueMapping::create([
                    'product_id' => $product->id,
                    'product_attribute_id' => $attributeId,
                    'product_attribute_value_id' => $valueId,
                ]);
            }
        }
    }

    /**
     * Lấy danh sách attributes của product theo format
     *
     * @return array [
     *               'color' => [
     *               'name' => 'Màu sắc',
     *               'values' => ['Red', 'Blue']
     *               ],
     *               'size' => [
     *               'name' => 'Kích thước',
     *               'values' => ['M', 'L']
     *               ]
     *               ]
     */
    public function getProductAttributesFormatted(Product $product): array
    {
        $mappings = $product->attributeMappings()->with('attribute', 'attributeValue')->get();

        $result = [];
        foreach ($mappings as $mapping) {
            $slug = $mapping->attribute->slug;

            if (! isset($result[$slug])) {
                $result[$slug] = [
                    'name' => $mapping->attribute->name,
                    'type' => $mapping->attribute->type,
                    'values' => [],
                ];
            }

            $result[$slug]['values'][] = [
                'id' => $mapping->attributeValue->id,
                'value' => $mapping->attributeValue->value,
                'display_value' => $mapping->attributeValue->display_name,
                'color_code' => $mapping->attributeValue->color_code,
            ];
        }

        return $result;
    }

    /**
     * Kiểm tra product có attribute value cụ thể không
     *
     * Ví dụ: $service->hasAttributeValue($product, 'color', 1)
     */
    public function hasAttributeValue(Product $product, string $attributeSlug, int $valueId): bool
    {
        return $product->attributeMappings()
            ->whereHas('attribute', fn ($q) => $q->where('slug', $attributeSlug))
            ->where('product_attribute_value_id', $valueId)
            ->exists();
    }

    /**
     * Lấy danh sách values của 1 attribute cho product
     *
     * Ví dụ: $service->getAttributeValues($product, 'color')
     * Kết quả: [1, 2] (ID của Red, Blue)
     */
    public function getAttributeValueIds(Product $product, string $attributeSlug): array
    {
        return $product->attributeMappings()
            ->whereHas('attribute', fn ($q) => $q->where('slug', $attributeSlug))
            ->pluck('product_attribute_value_id')
            ->toArray();
    }

    /**
     * Thêm 1 attribute value cho product
     *
     * Ví dụ: $service->addAttributeValue($product, $attribute, $value)
     */
    public function addAttributeValue(Product $product, ProductAttribute $attribute, ProductAttributeValue $value): ProductAttributeValueMapping
    {
        return ProductAttributeValueMapping::firstOrCreate(
            [
                'product_id' => $product->id,
                'product_attribute_id' => $attribute->id,
                'product_attribute_value_id' => $value->id,
            ]
        );
    }

    /**
     * Xóa 1 attribute value khỏi product
     */
    public function removeAttributeValue(Product $product, ProductAttribute $attribute, ProductAttributeValue $value): void
    {
        ProductAttributeValueMapping::where([
            'product_id' => $product->id,
            'product_attribute_id' => $attribute->id,
            'product_attribute_value_id' => $value->id,
        ])->delete();
    }

    /**
     * Lấy tất cả attributes có thể áp dụng cho category này
     */
    public function getAttributesByCategory(int $categoryId): Collection
    {
        // Lấy từ products trong category
        return ProductAttribute::whereHas(
            'products',
            fn ($q) => $q->where('product_category_id', $categoryId)
        )->distinct()->get();
    }

    /**
     * Xóa tất cả attributes của product
     */
    public function clearAttributes(Product $product): void
    {
        $product->attributeMappings()->delete();
    }
}
