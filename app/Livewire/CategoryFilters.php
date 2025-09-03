<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Subcategory;
use Livewire\Component;

class CategoryFilters extends Component
{
    public $categorySlug;
    public $category;
    public $selectedCategories = [];
    public $selectedSubcategories = [];
    public $expandedCategories = [];

    public function mount($categorySlug = null)
    {
        $this->categorySlug = $categorySlug;
        $this->loadCategory();
    }

    public function loadCategory()
    {
        if ($this->categorySlug) {
            $this->category = Category::where('slug', $this->categorySlug)
                ->where('categories.active', true)
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
        // If a specific category is selected, show only that category
        if ($this->category) {
            return collect([$this->category]);
        }

        // Otherwise show all active categories
        return Category::where('category.active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    public function getSubcategories($categoryId)
    {
        return Subcategory::where('category_id', $categoryId)
            ->where('subcategories.active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();
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
        $category = Category::find($categoryId);
        return $category ? $category->products()->where('products.active', true)->count() : 0;
    }

    public function getProductCountForSubcategory($subcategoryId)
    {
        $subcategory = Subcategory::find($subcategoryId);
        return $subcategory ? $subcategory->products()->where('products.active', true)->count() : 0;
    }

    public function render()
    {
        return view('livewire.category-filters');
    }
}
