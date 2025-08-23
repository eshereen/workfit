<?php

namespace App\Livewire;

use App\Models\Collection;
use Livewire\Component;

class CollectionsGrid extends Component
{
    public $collections;
    public $search = '';

    public function mount($collections = null)
    {
        if ($collections) {
            $this->collections = $collections;
        } else {
            $this->loadCollections();
        }
    }

    public function loadCollections()
    {
        $this->collections = Collection::where('active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    public function updatingSearch()
    {
        $this->loadCollections();
    }

    public function render()
    {
        $filteredCollections = $this->collections;

        if ($this->search) {
            $filteredCollections = $this->collections->filter(function ($collection) {
                return str_contains(strtolower($collection->name), strtolower($this->search));
            });
        }

        return view('livewire.collections-grid', [
            'filteredCollections' => $filteredCollections
        ]);
    }
}
