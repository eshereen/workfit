<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display the categories page with optional category filtering.
     */
    public function index($categorySlug = null)
    {
        if ($categorySlug) {
            // Show specific category
            $category = Category::where('slug', $categorySlug)
                ->where('active', true)
                ->with(['products.category', 'products.subcategory', 'products.media', 'products.variants'])
                ->firstOrFail();

            return view('categories', [
                'category' => $category,
                'categorySlug' => $category->slug
            ]);
        }

        // Show all categories (no specific category selected)
        return view('categories', [
            'category' => null,
            'categorySlug' => null
        ]);
    }

    /**
     * Display all categories.
     */
    public function all()
    {
        $categories = Category::where('active', true)
            ->withCount('products')
            ->get();

        return view('categories', [
            'category' => null,
            'categorySlug' => null
        ]);
    }

    /**
     * Display products for a specific subcategory within a category.
     */
    public function subcategory($categorySlug, $subcategorySlug)
    {
        $category = Category::where('categories.slug', $categorySlug)
            ->where('categories.active', true)
            ->firstOrFail();

        $subcategory = Subcategory::where('subcategories.slug', $subcategorySlug)
            ->where('category_id', $category->id)
            ->where('subcategories.active', true)
            ->with(['products.category', 'products.subcategory', 'products.media', 'products.variants'])
            ->firstOrFail();

        return view('subcategory', [
            'category' => $category,
            'subcategory' => $subcategory,
            'categorySlug' => $category->slug,
            'subcategorySlug' => $subcategory->slug
        ]);
    }
}
