<?php

namespace App\Models;

use Database\Factories\ProductVariantFactory;
use App\Traits\HasSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory, HasSku;

    protected $fillable = ['product_id', 'color', 'size', 'sku', 'stock', 'price', 'weight', 'quantity'];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
        'quantity' => 'integer',
    ];

    /**
     * Get color code with caching
     */
    public function getColorCodeAttribute()
    {
        return cache()->remember("color_code_{$this->color}", 3600, function () {
            return config('colors.' . $this->color, '#CCCCCC');
        });
    }

    /**
     * Get contrasting text color for background
     */
    public function getContrastColorAttribute()
    {
        $hex = ltrim($this->color_code, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    /**
     * Check if variant is in stock
     */
    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get stock status text
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'Out of Stock';
        } elseif ($this->stock <= 5) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to get variants in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope to get variants by color
     */
    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    /**
     * Scope to get variants by size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Get unique colors for a product with caching
     */
    public static function getUniqueColorsForProduct($productId)
    {
        return cache()->remember("product_colors_{$productId}", 1800, function () use ($productId) {
            return static::where('product_id', $productId)
                ->select('color')
                ->distinct()
                ->pluck('color');
        });
    }

    /**
     * Get unique sizes for a product with caching
     */
    public static function getUniqueSizesForProduct($productId)
    {
        return cache()->remember("product_sizes_{$productId}", 1800, function () use ($productId) {
            return static::where('product_id', $productId)
                ->select('size')
                ->distinct()
                ->pluck('size');
        });
    }

    /**
     * Get variants by color and size with caching
     */
    public static function getVariantsByColorAndSize($productId, $color, $size = null)
    {
        $cacheKey = "product_variants_{$productId}_{$color}" . ($size ? "_{$size}" : '');

        return cache()->remember($cacheKey, 900, function () use ($productId, $color, $size) {
            $query = static::where('product_id', $productId)->where('color', $color);

            if ($size) {
                $query->where('size', $size);
            }

            return $query->get();
        });
    }

    /**
     * Update stock with cache invalidation
     */
    public function updateStock($newStock)
    {
        $this->stock = $newStock;
        $this->save();

        // Clear related caches
        $this->clearRelatedCaches();
    }

    /**
     * Clear related caches when variant is updated
     */
    protected function clearRelatedCaches()
    {
        Cache::forget("product_colors_{$this->product_id}");
        Cache::forget("product_sizes_{$this->product_id}");
        Cache::forget("product_variants_{$this->product_id}_{$this->color}");
        Cache::forget("product_variants_{$this->product_id}_{$this->color}_{$this->size}");
    }

    /**
     * Boot method to clear caches on model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($variant) {
            $variant->clearRelatedCaches();
        });

        static::deleted(function ($variant) {
            $variant->clearRelatedCaches();
        });
    }
}
