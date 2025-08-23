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

     public function category()
    {
        return $this->belongsTo(Category::class);
    }

       public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function collection()
    {
        return $this->belongsTo(Collection::class);
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


    //register media collections
    public function registerMediaCollections(): void
    {
        // Main image (single image only)
        $this->addMediaCollection('main_image')
            ->singleFile() // Enforce only one image
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10);

                $this->addMediaConversion('medium')
                    ->width(400)
                    ->height(400);

                $this->addMediaConversion('large')
                    ->width(800)
                    ->height(800);
            });

        // Product gallery (multiple images)
        $this->addMediaCollection('product_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150);

                $this->addMediaConversion('medium')
                    ->width(600)
                    ->height(600);

                $this->addMediaConversion('zoom')
                    ->width(1200)
                    ->height(1200)
                    ->quality(85);
            });
    }


}
