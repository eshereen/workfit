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
        \Log::info('ProductSearch updatedSearch called', [
            'search' => $this->search,
            'length' => strlen($this->search)
        ]);

        if (strlen($this->search) >= 2) {
            try {
                $this->searchResults = Product::where('active', true)
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->with(['media', 'category', 'subcategory'])
                    ->take(5)
                    ->get();

                \Log::info('ProductSearch results found', [
                    'count' => $this->searchResults->count(),
                    'results' => $this->searchResults->pluck('name')
                ]);

                $this->showResults = true;
            } catch (\Exception $e) {
                \Log::error('ProductSearch error', [
                    'error' => $e->getMessage(),
                    'search' => $this->search
                ]);
                $this->searchResults = [];
                $this->showResults = false;
            }
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

    public function testSearch()
    {
        \Log::info('Test search method called');
        $this->search = 'test';
        $this->updatedSearch();
    }

    public function mount()
    {
        \Log::info('ProductSearch component mounted');
    }

    public function render()
    {
        \Log::info('ProductSearch render called', [
            'search' => $this->search,
            'results_count' => count($this->searchResults),
            'show_results' => $this->showResults
        ]);

        return view('livewire.product-search');
    }
}
