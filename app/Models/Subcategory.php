<?php

namespace App\Models;

use Database\Factories\SubcategoryFactory;
use App\Traits\Sluggable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Subcategory extends Model implements HasMedia
{
    /** @use HasFactory<SubcategoryFactory> */
    use HasFactory, Sluggable,InteractsWithMedia;
    protected $fillable = ['name', 'slug', 'description','parent_id', 'active'];

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
        }

        public function category()
        {
            return $this->belongsTo(Category::class);
        }
}
