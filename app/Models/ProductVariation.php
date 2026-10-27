<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variations';

    protected $fillable = [
        'post_id',
        'sku',
        'price',
        'sale_price',
        'manage_stock',
        'stock_quantity',
        'stock_status',
        'image',
        'attributes',
        'attribute_hash',
        'status',
        'order',
    ];

    protected $casts = [
        'attributes' => 'array',
        'manage_stock' => 'boolean',
        'price' => 'float',
        'sale_price' => 'float',
        'stock_quantity' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Compute canonical attribute hash to guarantee uniqueness per product.
     */
    public static function computeAttributeHash(array $attributes): string
    {
        $normalized = [];
        foreach ($attributes as $key => $val) {
            $k = trim(mb_strtolower((string) $key));
            $v = trim(mb_strtolower((string) $val));
            if ($k !== '' && $v !== '') {
                $normalized[$k] = $v;
            }
        }
        ksort($normalized);

        return hash('sha256', json_encode($normalized, JSON_UNESCAPED_UNICODE));
    }

    protected static function booted()
    {
        static::saving(function (ProductVariation $variation) {
            if (empty($variation->attribute_hash) && ! empty($variation->attributes)) {
                $variation->attribute_hash = static::computeAttributeHash($variation->attributes);
            }

            if ($variation->manage_stock) {
                if ($variation->stock_quantity <= 0) {
                    $variation->stock_status = 'out_of_stock';
                } elseif (empty($variation->stock_status) || in_array($variation->stock_status, ['out_of_stock', 'outofstock'])) {
                    $variation->stock_status = 'in_stock';
                }
            } else {
                if ($variation->stock_status === 'instock') {
                    $variation->stock_status = 'in_stock';
                } elseif ($variation->stock_status === 'outofstock') {
                    $variation->stock_status = 'out_of_stock';
                }
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'post_id');
    }

    /**
     * Check if this variation has a specific attribute value.
     */
    public function matchesAttribute(string $name, string $value): bool
    {
        $attrs = (array) ($this->attributes ?? []);
        foreach ($attrs as $k => $v) {
            if (mb_strtolower(trim($k)) === mb_strtolower(trim($name))) {
                return mb_strtolower(trim($v)) === mb_strtolower(trim($value));
            }
        }

        return false;
    }

    /**
     * Get specific attribute value from variation.
     */
    public function findAttributeValue(string $name): ?string
    {
        $attrs = (array) ($this->attributes ?? []);
        foreach ($attrs as $k => $v) {
            if (mb_strtolower(trim($k)) === mb_strtolower(trim($name))) {
                return (string) $v;
            }
        }

        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && $this->sale_price !== '' && (float) $this->sale_price > 0) {
            return (float) $this->sale_price;
        }

        return (float) ($this->price ?? 0);
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = $this->effective_price;
        if ($price <= 0) {
            return 'Liên hệ';
        }

        return number_format($price, 0, ',', '.').' ₫';
    }

    public function getFormattedRegularPriceAttribute(): string
    {
        $price = (float) ($this->price ?? 0);
        if ($price <= 0) {
            return 'Liên hệ';
        }

        return number_format($price, 0, ',', '.').' ₫';
    }

    public function getFormattedSalePriceAttribute(): ?string
    {
        if ($this->sale_price !== null && (float) $this->sale_price > 0) {
            return number_format((float) $this->sale_price, 0, ',', '.').' ₫';
        }

        return null;
    }

    public function getIsInStockAttribute(): bool
    {
        $isOut = in_array($this->stock_status, ['out_of_stock', 'outofstock'], true);
        if ($this->manage_stock) {
            return $this->stock_quantity > 0 && ! $isOut;
        }

        return ! $isOut;
    }

    public function getNameAttribute(): string
    {
        if (empty($this->attributes)) {
            return $this->sku ?: 'Biến thể #'.$this->id;
        }

        return implode(' - ', array_values($this->attributes));
    }
}
