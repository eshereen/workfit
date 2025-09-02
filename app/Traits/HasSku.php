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
        $categoryCode = $this->getCategoryCode();
        $subcategoryCode = $this->getSubcategoryCode();
        $productCode = $this->getProductCode();
        $uniqueId = $this->generateUniqueId();

        return "{$categoryCode}-{$subcategoryCode}-{$productCode}-{$uniqueId}";
    }

    protected function getCategoryCode(): string
    {
        if (!empty($this->category_id) && $this->category) {
            return strtoupper(substr(
                preg_replace('/[^A-Z0-9]/', '', $this->category->name),
                0,
                3
            ));
        }

        return 'GEN';
    }

    protected function getSubcategoryCode(): string
    {
        if (!empty($this->subcategory_id) && $this->subcategory) {
            return strtoupper(substr(
                preg_replace('/[^A-Z0-9]/', '', $this->subcategory->name),
                0,
                3
            ));
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
        if (!empty($this->category_id)) {
            $nextId = $this->getNextIdForCategory($this->category_id);
        } else {
            $nextId = static::count() + 1;
        }

        return str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    protected function getNextIdForCategory($categoryId): int
    {
        $count = static::where('category_id', $categoryId)->count();
        return $count + 1;
    }
}
