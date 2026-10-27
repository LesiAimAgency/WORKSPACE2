<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'display_price' => (float) $this->display_price,
            'discount_percent' => $this->sale_price ? round((1 - $this->sale_price / $this->price) * 100) : 0,
            'stock_quantity' => $this->stock_quantity,
            'stock_status' => $this->stock_status,
            'featured_image' => $this->featured_image,
            'gallery' => $this->gallery,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'brand' => [
                'id' => $this->brand?->id,
                'name' => $this->brand?->name,
                'slug' => $this->brand?->slug,
            ],
            'is_featured' => $this->is_featured,
            'rating_average' => (float) $this->rating_average,
            'rating_count' => $this->rating_count,
            'views' => $this->views,
            'status' => $this->status,
            'product_type' => $this->product_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
