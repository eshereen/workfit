<?php
namespace App\Traits;

trait HasSku
{
    protected static function bootHasSku()
    {
        static::creating(function ($model) {
            if (empty($model->sku)) {
                $model->sku = $model->generateSku();
            }
        });
    }

    public function generateSku(): string
    {
        // Get category code with fallback
        $categoryCode = $this->getCategoryCode();

        // Get subcategory code with fallback
        $subcategoryCode = $this->getSubcategoryCode();

        // Get product code with fallback
        $productCode = $this->getProductCode();

        // Generate unique identifier
        $uniqueId = $this->generateUniqueId();

        return "{$categoryCode}-{$subcategoryCode}-{$productCode}-{$uniqueId}";
    }

    /**
     * Get category code with fallback
     */
    protected function getCategoryCode(): string
    {
        if ($this->category && $this->category->exists) {
            return strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $this->category->name), 0, 3));
        }

        return 'GEN'; // Fallback for "General"
    }

    /**
     * Get subcategory code with fallback
     */
    protected function getSubcategoryCode(): string
    {
        if ($this->subcategory && $this->subcategory->exists) {
            return strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $this->subcategory->name), 0, 3));
        }

        return 'GEN'; // Fallback for "General"
    }

    /**
     * Get product code with fallback
     */
    protected function getProductCode(): string
    {
        if (!empty($this->name)) {
            $cleanName = preg_replace('/[^A-Z0-9]/', '', strtoupper($this->name));
            if (!empty($cleanName)) {
                return substr($cleanName, 0, 4);
            }
        }

        return 'PRD'; // Fallback for "Product"
    }

    /**
     * Generate unique identifier for SKU
     */
    protected function generateUniqueId(): string
    {
        // Try to get the next sequential number for the same category
        if ($this->category && $this->category->exists) {
            $nextId = $this->getNextIdForCategory($this->category->id);
        } else {
            // Fallback to overall product count
            $nextId = static::count() + 1;
        }

        return str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get next sequential ID for products in the same category
     */
    protected function getNextIdForCategory($categoryId): int
    {
        // Count products with the same category
        $count = static::where('category_id', $categoryId)->count();

        return $count + 1;
    }
}
