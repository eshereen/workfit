<?php

namespace App\Observers;

use App\Models\Product;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Clear all product-related cache
     */
    private function clearCache(): void
    {
        try {
            ProductController::clearProductCache();
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::error('Failed to clear product cache: ' . $e->getMessage());
        }
    }
}
