<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Subcategory;

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
        $categoryCode = $this->getCategoryCode();
        $subcategoryCode = $this->getSubcategoryCode();
        $productCode = $this->getProductCode();
        $uniqueId = $this->generateUniqueId();

        return "{$categoryCode}-{$subcategoryCode}-{$productCode}-{$uniqueId}";
    }

    protected function getCategoryCode(): string
    {
        // If subcategory is set, fetch category via subcategory
        if (!empty($this->subcategory_id)) {
            $subcategory = Subcategory::with('category')->find($this->subcategory_id);
            if ($subcategory && $subcategory->category) {
                return strtoupper(substr(
                    preg_replace('/[^A-Z0-9]/', '', $subcategory->category->name),
                    0,
                    3
                ));
            }
        }

        // If product has category_id directly
        if (!empty($this->category_id)) {
            $category = Category::find($this->category_id);
            if ($category) {
                return strtoupper(substr(
                    preg_replace('/[^A-Z0-9]/', '', $category->name),
                    0,
                    3
                ));
            }
        }

        return 'GEN';
    }

    protected function getSubcategoryCode(): string
    {
        if (!empty($this->subcategory_id)) {
            $subcategory = Subcategory::find($this->subcategory_id);
            if ($subcategory) {
                return strtoupper(substr(
                    preg_replace('/[^A-Z0-9]/', '', $subcategory->name),
                    0,
                    3
                ));
            }
        }

        return 'GEN';
    }

    protected function getProductCode(): string
    {
        if (!empty($this->name)) {
            $cleanName = preg_replace('/[^A-Z0-9]/', '', strtoupper($this->name));
            if (!empty($cleanName)) {
                return substr($cleanName, 0, 4);
            }
        }

        return 'PRD';
    }

    protected function generateUniqueId(): string
    {
        if (!empty($this->subcategory_id)) {
            $nextId = $this->getNextIdForSubcategory($this->subcategory_id);
        } elseif (!empty($this->category_id)) {
            $nextId = $this->getNextIdForCategory($this->category_id);
        } else {
            $nextId = static::count() + 1;
        }

        return str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    protected function getNextIdForSubcategory($subcategoryId): int
    {
        $count = static::where('subcategory_id', $subcategoryId)->count();
        return $count + 1;
    }

    protected function getNextIdForCategory($categoryId): int
    {
        $count = static::where('category_id', $categoryId)->count();
        return $count + 1;
    }
}
