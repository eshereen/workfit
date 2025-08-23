<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategoriesGrid extends Component
{
    public $categories;
    public $search = '';

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::where('active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    public function updatingSearch()
    {
        $this->loadCategories();
    }

    public function render()
    {
        $filteredCategories = $this->categories;

        if ($this->search) {
            $filteredCategories = $this->categories->filter(function ($category) {
                return str_contains(strtolower($category->name), strtolower($this->search));
            });
        }

        return view('livewire.categories-grid', [
            'filteredCategories' => $filteredCategories
        ]);
    }
}
