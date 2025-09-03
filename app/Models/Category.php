<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use App\Traits\Sluggable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory,Sluggable, InteractsWithMedia;
    protected $fillable = ['name', 'slug', 'description','parent_id', 'active', 'featured'];


    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Subcategory::class,
            'category_id',   // Foreign key on subcategories table
            'subcategory_id', // Foreign key on products table
            'id',             // Local key on categories table
            'id'              // Local key on subcategories table
        );
    }


    /**
     * Get the optimized media URL for the category
     * This method is cached to avoid N+1 queries
     */
    public function getOptimizedMediaUrl()
    {
        return cache()->remember("category_media_{$this->id}", 3600, function () {
            return $this->getFirstMediaUrl('main_image', 'medium_webp');
        });
    }

    /**
     * Get active products count with caching
     */
    public function getActiveProductsCount()
    {
        return cache()->remember("category_products_count_{$this->id}", 1800, function () {
            return $this->products()->where('active', true)->count();
        });
    }
     //register media collections
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


}
}
