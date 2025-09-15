<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductSearch extends Component
{
    public $search = '';
    public $searchResults = [];
    public $showResults = false;

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->searchResults = Product::where('products.active', true)
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhereHas('category', function($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('subcategory', function($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          });
                })
                ->with(['media', 'category', 'subcategory'])
                ->take(5)
                ->get();

            $this->showResults = true;
        } else {
            $this->searchResults = [];
            $this->showResults = false;
        }
    }

    public function selectProduct($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            return redirect()->route('product.show', $product->slug);
        }
    }

    public function searchAll()
    {
        if (strlen($this->search) >= 2) {
            return redirect()->route('products.search', ['q' => $this->search]);
        }
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
