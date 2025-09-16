<?php

namespace App\Services;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BestSellerService
{
    /**
     * Get best selling products based on order data
     */
    public function getBestSellingProducts($limit = 10, $categoryId = null, $collectionId = null)
    {
        $cacheKey = "best_selling_products_{$limit}_" . ($categoryId ? "cat_{$categoryId}" : 'all') . ($collectionId ? "_col_{$collectionId}" : '');
        
        return Cache::remember($cacheKey, 1800, function () use ($limit, $categoryId, $collectionId) {
            $query = OrderItem::select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->where('orders.created_at', '>=', now()->subMonths(3)) // Last 3 months
                ->groupBy('order_items.product_id')
                ->orderBy('total_sold', 'desc');

            if ($categoryId) {
                $query->join('products', 'order_items.product_id', '=', 'products.id')
                      ->where('products.category_id', $categoryId);
            }

            if ($collectionId) {
                $query->join('products', 'order_items.product_id', '=', 'products.id')
                      ->join('collection_products', 'products.id', '=', 'collection_products.product_id')
                      ->where('collection_products.collection_id', $collectionId);
            }

            $bestSellerIds = $query->limit($limit)->pluck('order_items.product_id');

            return $bestSellerIds->toArray();
        });
    }

    /**
     * Get products with best seller priority (best sellers first, then featured, then regular)
     */
    public function getProductsWithBestSellerPriority($baseQuery, $limit = null, $categoryId = null, $collectionId = null)
    {
        $bestSellerIds = $this->getBestSellingProducts($limit ?: 50, $categoryId, $collectionId);
        
        if (empty($bestSellerIds)) {
            // If no best sellers, prioritize featured products
            return $baseQuery->orderBy('featured', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->when($limit, fn($q) => $q->limit($limit))
                           ->get();
        }

        // Get best sellers first
        $bestSellers = $baseQuery->whereIn('id', $bestSellerIds)
                               ->orderByRaw('FIELD(id, ' . implode(',', $bestSellerIds) . ')')
                               ->get();

        // Get remaining products (featured first, then regular)
        $remainingLimit = $limit ? $limit - $bestSellers->count() : null;
        if ($remainingLimit > 0) {
            $remainingProducts = $baseQuery->whereNotIn('id', $bestSellerIds)
                                         ->orderBy('featured', 'desc')
                                         ->orderBy('created_at', 'desc')
                                         ->limit($remainingLimit)
                                         ->get();

            return $bestSellers->merge($remainingProducts);
        }

        return $bestSellers;
    }

    /**
     * Check if a product is a best seller
     */
    public function isBestSeller($productId, $categoryId = null, $collectionId = null)
    {
        $bestSellerIds = $this->getBestSellingProducts(50, $categoryId, $collectionId);
        return in_array($productId, $bestSellerIds);
    }

    /**
     * Clear best seller cache
     */
    public function clearCache()
    {
        Cache::flush(); // Clear all cache, or implement more specific cache clearing
    }
}
