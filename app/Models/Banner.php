<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'title',
        'description',
        'media_type',
        'image',
        'video',
        'poster_image',
        'button_text',
        'link_type',
        'category_id',
        'subcategory_id',
        'custom_url',
        'button_text_2',
        'link_type_2',
        'category_id_2',
        'subcategory_id_2',
        'custom_url_2',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Check if banner uses video
     */
    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }

    /**
     * Check if banner uses image
     */
    public function isImage(): bool
    {
        return $this->media_type === 'image';
    }

    /**
     * Get the category this banner links to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory this banner links to
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Get the full URL for the banner's link
     */
    public function getLink(): ?string
    {
        return match($this->link_type) {
            'category' => $this->category ? route('categories.index', $this->category->slug) : null,
            'subcategory' => $this->subcategory && $this->category 
                ? route('categories.subcategory', [$this->category->slug, $this->subcategory->slug]) 
                : null,
            'url' => $this->custom_url,
            default => null,
        };
    }

    /**
     * Get the category for button 2
     */
    public function category2()
    {
        return $this->belongsTo(Category::class, 'category_id_2');
    }

    /**
     * Get the subcategory for button 2
     */
    public function subcategory2()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id_2');
    }

    /**
     * Get the full URL for the banner's second button link
     */
    public function getLink2(): ?string
    {
        return match($this->link_type_2) {
            'category' => $this->category2 ? route('categories.index', $this->category2->slug) : null,
            'subcategory' => $this->subcategory2 && $this->category2 
                ? route('categories.subcategory', [$this->category2->slug, $this->subcategory2->slug]) 
                : null,
            'url' => $this->custom_url_2,
            default => null,
        };
    }

    /**
     * Get the full image URL
     */
    public function getImageUrl(): string
    {
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        
        // Check for WebP version first
        $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $this->image);
        if (Storage::disk('public')->exists($webpPath)) {
            return Storage::disk('public')->url($webpPath);
        }
        
        return Storage::disk('public')->url($this->image);
    }

    /**
     * Get the full video URL
     */
    public function getVideoUrl(): ?string
    {
        if (!$this->video) {
            return null;
        }

        if (filter_var($this->video, FILTER_VALIDATE_URL)) {
            return $this->video;
        }
        
        return Storage::disk('public')->url($this->video);
    }

    /**
     * Get the full poster image URL for videos
     */
    public function getPosterImageUrl(): ?string
    {
        if (!$this->poster_image) {
            return null;
        }

        if (filter_var($this->poster_image, FILTER_VALIDATE_URL)) {
            return $this->poster_image;
        }
        
        // Check for WebP version first
        $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $this->poster_image);
        if (Storage::disk('public')->exists($webpPath)) {
            return Storage::disk('public')->url($webpPath);
        }
        
        return Storage::disk('public')->url($this->poster_image);
    }

    /**
     * Scope to get only active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get banners by section
     */
    public function scopeSection($query, string $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get banner by section (helper method)
     */
    public static function getBySection(string $section)
    {
        return static::active()
            ->section($section)
            ->ordered()
            ->first();
    }

    /**
     * Get multiple banners by section pattern
     */
    public static function getBySectionPattern(string $pattern)
    {
        return static::active()
            ->where('section', 'like', $pattern)
            ->ordered()
            ->get();
    }
}
