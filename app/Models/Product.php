<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use App\Traits\Sluggable;
use Filament\Tables\Columns\Summarizers\Sum;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Exception;
use Illuminate\Support\Facades\Log;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, InteractsWithMedia, Sluggable;
    protected $fillable = ['name', 'slug', 'description', 'price', 'compare_price','featured', 'active','category_id','subcategory_id'];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'featured' => 'boolean',
        'active' => 'boolean',
    ];

    protected $appends = ['quantity', 'has_variants'];

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Product';
    }

    /**
     * Get the quantity attribute (sum of all variant quantities or 0 if no variants)
     */
    public function getQuantityAttribute()
    {
        if ($this->variants->count() > 0) {
            return $this->variants->sum('stock');
        }
        return 0; // Products without variants are considered out of stock for cart purposes
    }


     public function category()
    {
        return $this->belongsTo(Category::class);
    }

       public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_products', 'product_id', 'collection_id')
            ->using(CollectionProduct::class)
            ->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get variants with optimized loading
     */
    public function variantsOptimized()
    {
        return $this->hasMany(ProductVariant::class)
            ->select('id', 'product_id', 'color', 'size', 'sku', 'stock', 'price', 'weight')
            ->orderBy('color')
            ->orderBy('size');
    }

    /**
     * Get variants in stock
     */
    public function variantsInStock()
    {
        return $this->hasMany(ProductVariant::class)->where('stock', '>', 0);
    }

    /**
     * Get unique colors for this product
     */
    public function getUniqueColors()
    {
        return ProductVariant::getUniqueColorsForProduct($this->id);
    }

    /**
     * Get unique sizes for this product
     */
    public function getUniqueSizes()
    {
        return ProductVariant::getUniqueSizesForProduct($this->id);
    }

    /**
     * Get variants by color
     */
    public function getVariantsByColor($color)
    {
        return ProductVariant::getVariantsByColorAndSize($this->id, $color);
    }

    /**
     * Get variant by color and size
     */
    public function getVariantByColorAndSize($color, $size)
    {
        return ProductVariant::getVariantsByColorAndSize($this->id, $color, $size)->first();
    }

    /**
     * Check if product has variants
     */
    public function getHasVariantsAttribute()
    {
        // If variants are already loaded, use the collection
        if ($this->relationLoaded('variants') || $this->relationLoaded('variantsOptimized')) {
            return $this->variants && $this->variants->isNotEmpty();
        }

        // Otherwise, use cached query
        return cache()->remember("product_has_variants_{$this->id}", 1800, function () {
            return $this->variants()->exists();
        });
    }

    /**
     * Get variants count
     */
    public function getVariantsCountAttribute()
    {
        // If variants are already loaded, use the collection
        if ($this->relationLoaded('variants') || $this->relationLoaded('variantsOptimized')) {
            return $this->variants ? $this->variants->count() : 0;
        }

        // Otherwise, use cached query
        return cache()->remember("product_variants_count_{$this->id}", 1800, function () {
            return $this->variants()->count();
        });
    }



    public function wishlists()
    {
        return $this->belongsToMany(Wishlist::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }



    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price > 0) {
            return round(100 - ($this->price / $this->compare_price * 100));
        }
        return 0;
    }

    /**
     * Format price to remove .00 for whole numbers
     * 
     * 
     * @param float|null $price
     * @return string
     */
    public static function formatPrice($price)
    {
        if ($price === null) {
            return '0';
        }
        
        // Check if price is a whole number
        return (floor($price) == $price) 
            ? number_format($price, 0) 
            : number_format($price, 2);
    }

    /**
     * Safely get media URL with error handling
     */
    public function getSafeMediaUrl($collectionName = 'main_image', $conversionName = '')
    {
        try {
            if (!$this->media || $this->media->isEmpty()) {
                return null;
            }

            $media = $this->media->where('collection_name', $collectionName)->first();
            if (!$media || !$media->disk) {
                return null;
            }

            return $this->getFirstMediaUrl($collectionName, $conversionName);
        } catch (Exception $e) {
            Log::warning('Failed to get media URL for product', [
                'product_id' => $this->id,
                'collection' => $collectionName,
                'conversion' => $conversionName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

// Register media collections
public function registerMediaCollections(?Media $media = null): void
{
    // Main image (single file)
    $this->addMediaCollection('main_image')
        ->singleFile()
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
        ->registerMediaConversions(function (Media $media) {

            // Always keep original (JPG/PNG/etc.)
            // Convert optimized versions:

            // WebP versions
            $this->addMediaConversion('small_webp')
                ->format('webp')
                ->width(300)
                ->height(300)

                ->queued();

            $this->addMediaConversion('thumb_webp')
                ->format('webp')
                ->width(150)
                ->height(150)
                ->sharpen(10)

                ->queued();

            $this->addMediaConversion('medium_webp')
                ->format('webp')
                ->width(400)
                ->height(400)

                ->queued();

            $this->addMediaConversion('large_webp')
                ->format('webp')
                ->width(800)
                ->height(800)


                ->queued();


            // AVIF conversions disabled - server doesn't support AVIF
            /*
            $this->addMediaConversion('thumb_avif')
                ->format('avif')
                ->width(150)
                ->height(150)


                ->queued();

            $this->addMediaConversion('medium_avif')
                ->format('avif')
                ->width(400)
                ->height(400)


                ->queued();

            $this->addMediaConversion('large_avif')
                ->format('avif')
                ->width(800)
                ->height(800)


                ->queued();
            */
        });

    // Product gallery (multiple images)
    $this->addMediaCollection('product_images')
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
        ->registerMediaConversions(function (Media $media) {

            // WebP versions
            $this->addMediaConversion('thumb_webp')
                ->format('webp')
                ->width(150)
                ->height(150)
                ->queued();

            $this->addMediaConversion('medium_webp')
                ->format('webp')
                ->width(600)
                ->height(600)
                ->queued();
                $this->addMediaConversion('large_webp')
                ->format('webp')
                ->width(800)
                ->height(800)
                ->queued();

            $this->addMediaConversion('zoom_webp')
                ->format('webp')
                ->width(1200)
                ->height(1200)
                ->queued();


            // AVIF conversions disabled - server doesn't support AVIF
            /*
            $this->addMediaConversion('thumb_avif')
                ->format('avif')
                ->width(150)
                ->height(150)
                ->queued();

            $this->addMediaConversion('medium_avif')
                ->format('avif')
                ->width(600)
                ->height(600)
                ->queued();

            $this->addMediaConversion('zoom_avif')
                ->format('avif')
                ->width(1200)
                ->height(1200)
                ->queued();
            */
        });
}



}
