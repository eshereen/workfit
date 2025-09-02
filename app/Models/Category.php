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
    protected $fillable = ['name', 'slug', 'description','parent_id', 'active'];


    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
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
