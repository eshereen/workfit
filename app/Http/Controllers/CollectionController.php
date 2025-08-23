<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Display the specified collection.
     */
    public function show($slug)
    {
        $collection = Collection::where('slug', $slug)
            ->where('active', true)
            ->with(['products.category', 'products.subcategory', 'products.media', 'products.variants'])
            ->firstOrFail();

        return view('collection', [
            'collection' => $collection,
            'collectionSlug' => $collection->slug
        ]);
    }

    /**
     * Display all collections.
     */
    public function index()
    {
        $collections = Collection::where('active', true)
            ->withCount('products')
            ->get();

        return view('collections.index', compact('collections'));
    }

    /**
     * Get collection data as JSON (for AJAX requests).
     */
    public function getCollectionData($slug)
    {
        $collection = Collection::where('slug', $slug)
            ->where('active', true)
            ->with(['products.category', 'products.subcategory', 'products.media', 'products.variants'])
            ->first();

        if (!$collection) {
            return response()->json(['error' => 'Collection not found'], 404);
        }

        return response()->json([
            'collection' => $collection,
            'products_count' => $collection->products()->count(),
            'has_media' => $collection->media->count() > 0
        ]);
    }
}
