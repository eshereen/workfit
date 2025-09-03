<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Collection as CollectionModel;
use Livewire\Component;

class CollectionFilters extends Component
{
    public $collectionSlug;
    public $collection;
    public $selectedCategories = [];
    public $selectedSubcategories = [];
    public $expandedCategories = [];

    public function mount($collectionSlug)
    {
        $this->collectionSlug = $collectionSlug;
        $this->loadCollection();
    }

    public function loadCollection()
    {
        if ($this->collectionSlug) {
            $this->collection = CollectionModel::where('slug', $this->collectionSlug)
                ->where('collections.active', true)
                ->with(['products.category', 'products.subcategory'])
                ->first();
        }
    }

    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->expandedCategories)) {
            $this->expandedCategories = array_filter($this->expandedCategories, function($id) use ($categoryId) {
                return $id != $categoryId;
            });
        } else {
            $this->expandedCategories[] = $categoryId;
        }
    }

    public function toggleCategorySelection($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_filter($this->selectedCategories, function($id) use ($categoryId) {
                return $id != $categoryId;
            });
            // Remove all subcategories of this category
            $this->selectedSubcategories = array_filter($this->selectedSubcategories, function($subId) use ($categoryId) {
                $subcategory = $this->getSubcategories($categoryId)->firstWhere('id', $subId);
                return !$subcategory;
            });
        } else {
            $this->selectedCategories[] = $categoryId;
        }

        $this->emitFiltersChanged();
    }

    public function toggleSubcategorySelection($subcategoryId, $categoryId)
    {
        if (in_array($subcategoryId, $this->selectedSubcategories)) {
            $this->selectedSubcategories = array_filter($this->selectedSubcategories, function($id) use ($subcategoryId) {
                return $id != $subcategoryId;
            });
        } else {
            $this->selectedSubcategories[] = $subcategoryId;
        }

        $this->emitFiltersChanged();
    }

    public function emitFiltersChanged()
    {
        $this->dispatch('filtersChanged', [
            'categories' => $this->selectedCategories,
            'subcategories' => $this->selectedSubcategories
        ]);
    }

    public function clearFilters()
    {
        $this->selectedCategories = [];
        $this->selectedSubcategories = [];
        $this->emitFiltersChanged();
    }

    public function getCategories()
    {
        if (!$this->collection) {
            // If no specific collection, get categories from all active products
            return Category::where('category.active', true)
                ->withCount('products')
                ->orderBy('name')
                ->get();
        }

        return $this->collection->products
            ->pluck('category')
            ->unique('id')
            ->filter()
            ->sortBy('name');
    }

    public function getSubcategories($categoryId)
    {
        if (!$this->collection) {
            // If no specific collection, get subcategories from all active products
            return Subcategory::where('category_id', $categoryId)
                ->where('subcategories.active', true)
                ->withCount('products')
                ->orderBy('name')
                ->get();
        }

        return $this->collection->products
            ->where('category_id', $categoryId)
            ->pluck('subcategory')
            ->unique('id')
            ->filter()
            ->sortBy('name');
    }

    public function isCategorySelected($categoryId)
    {
        return in_array($categoryId, $this->selectedCategories);
    }

    public function isSubcategorySelected($subcategoryId)
    {
        return in_array($subcategoryId, $this->selectedSubcategories);
    }

    public function isCategoryExpanded($categoryId)
    {
        return in_array($categoryId, $this->expandedCategories);
    }

    public function getProductCountForCategory($categoryId)
    {
        if (!$this->collection) {
            // If no specific collection, get count from all active products
            return Product::where('category_id', $categoryId)
                ->where('products.active', true)
                ->count();
        }

        return $this->collection->products
            ->where('category_id', $categoryId)
            ->count();
    }

    public function getProductCountForSubcategory($subcategoryId)
    {
        if (!$this->collection) {
            // If no specific collection, get count from all active products
            return Product::where('subcategory_id', $subcategoryId)
                ->where('products.active', true)
                ->count();
        }

        return $this->collection->products
            ->where('subcategory_id', $subcategoryId)
            ->count();
    }

    public function render()
    {
        return view('livewire.collection-filters');
    }
}
