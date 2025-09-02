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

class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, InteractsWithMedia, Sluggable;
    protected $fillable = ['name', 'slug', 'description', 'price', 'compare_price','featured', 'active','category_id','subcategory_id'];

    protected $casts = [

        'featured' => 'boolean',
        'active' => 'boolean',

    ];
    public function __toString(): string
{
    return $this->name ?? 'Unnamed Product';
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
            $this->addMediaConversion('thumb_webp')
                ->format('webp')
                ->width(150)
                ->height(150)
                ->sharpen(10)
                ->quality(85) // balance quality & compression
                ->nonQueued();

            $this->addMediaConversion('medium_webp')
                ->format('webp')
                ->width(400)
                ->height(400)
                ->quality(85)
                ->nonQueued();

            $this->addMediaConversion('large_webp')
                ->format('webp')
                ->width(800)
                ->height(800)
                ->quality(85)
                ->nonQueued();

            // AVIF (optional — smaller but more CPU heavy)
            $this->addMediaConversion('thumb_avif')
                ->format('avif')
                ->width(150)
                ->height(150)
                ->quality(80)
                ->nonQueued();

            $this->addMediaConversion('medium_avif')
                ->format('avif')
                ->width(400)
                ->height(400)
                ->quality(80)
                ->nonQueued();

            $this->addMediaConversion('large_avif')
                ->format('avif')
                ->width(800)
                ->height(800)
                ->quality(80)
                ->nonQueued();
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
                ->quality(85)
                ->nonQueued();

            $this->addMediaConversion('medium_webp')
                ->format('webp')
                ->width(600)
                ->height(600)
                ->quality(85)
                ->nonQueued();

            $this->addMediaConversion('zoom_webp')
                ->format('webp')
                ->width(1200)
                ->height(1200)
                ->quality(85)
                ->nonQueued();

            // AVIF versions (optional)
            $this->addMediaConversion('thumb_avif')
                ->format('avif')
                ->width(150)
                ->height(150)
                ->quality(80)
                ->nonQueued();

            $this->addMediaConversion('medium_avif')
                ->format('avif')
                ->width(600)
                ->height(600)
                ->quality(80)
                ->nonQueued();

            $this->addMediaConversion('zoom_avif')
                ->format('avif')
                ->width(1200)
                ->height(1200)
                ->quality(80)
                ->nonQueued();
        });
}



}
