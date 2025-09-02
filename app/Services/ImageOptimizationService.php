<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageOptimizationService
{
    /**
     * Get optimized image URL with fallback
     */
    public static function getOptimizedImageUrl($model, $collection = 'main_image', $conversion = 'medium_webp', $fallback = '/imgs/workfit.png')
    {
        try {
            $imageUrl = $model->getFirstMediaUrl($collection, $conversion);
            
            if (!$imageUrl) {
                // Try without conversion
                $imageUrl = $model->getFirstMediaUrl($collection);
            }
            
            if (!$imageUrl) {
                return $fallback;
            }
            
            return $imageUrl;
        } catch (\Exception $e) {
            Log::warning('Image optimization error', [
                'model' => get_class($model),
                'model_id' => $model->id ?? 'unknown',
                'collection' => $collection,
                'conversion' => $conversion,
                'error' => $e->getMessage()
            ]);
            
            return $fallback;
        }
    }

    /**
     * Get optimized image data for product index
     */
    public static function getProductImageData($product)
    {
        $cacheKey = "product_image_data_{$product->id}";
        
        return Cache::remember($cacheKey, 1800, function () use ($product) {
            $mainImage = self::getOptimizedImageUrl($product, 'main_image', 'medium_webp');
            $galleryImage = self::getOptimizedImageUrl($product, 'product_images', 'medium_webp');
            
            return [
                'main_image' => $mainImage,
                'main_image_avif' => self::getOptimizedImageUrl($product, 'main_image', 'medium_avif'),
                'main_image_webp' => $mainImage,
                'gallery_image' => $galleryImage,
                'gallery_image_avif' => self::getOptimizedImageUrl($product, 'product_images', 'medium_avif'),
                'gallery_image_webp' => $galleryImage,
            ];
        });
    }

    /**
     * Preload critical images
     */
    public static function preloadCriticalImages($products)
    {
        $criticalImages = [];
        
        foreach ($products->take(4) as $product) {
            $imageData = self::getProductImageData($product);
            $criticalImages[] = $imageData['main_image_webp'];
        }
        
        return $criticalImages;
    }

    /**
     * Generate responsive image sizes
     */
    public static function getResponsiveImageSizes($model, $collection = 'main_image')
    {
        $sizes = [
            'small' => self::getOptimizedImageUrl($model, $collection, 'small_webp'),
            'medium' => self::getOptimizedImageUrl($model, $collection, 'medium_webp'),
            'large' => self::getOptimizedImageUrl($model, $collection, 'large_webp'),
        ];
        
        return $sizes;
    }
}
