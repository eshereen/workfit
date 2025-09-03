<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        $title = 'WorkFit|Home';

        // Load main categories with their own products only
        $categories = cache()->remember('home_categories', 1800, function () {
            return Category::where('active', true)
                ->take(4)
                ->get()
                ->map(function ($category) {
                    // Load products belonging directly to this category or its subcategories
                    $products = Product::with(['media' => function ($q) {
                            $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                              ->where('collection_name', 'main_image')
                              ->whereNotNull('disk')
                              ->limit(1);
                        }, 'category:id,name,slug', 'subcategory:id,name,slug,category_id'])
                        ->where('active', true)
                        ->where(function ($q) use ($category) {
                            $q->where('category_id', $category->id)
                              ->orWhereHas('subcategory', function ($sub) use ($category) {
                                  $sub->where('category_id', $category->id);
                              });
                        })
                        ->latest('created_at')
                        ->take(8)
                        ->get();

                    $category->setRelation('products', $products);

                    return $category;
                });
        });

        // Recent Products
        $recent = cache()->remember('home_recent_products', 1800, function () {
            return Product::with(['category:id,name,slug'])
                ->with(['media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                          ->where('collection_name', 'main_image')
                          ->whereNotNull('disk')
                          ->limit(1);
                }])
                ->where('active', true)
                ->whereHas('media', function ($q) {
                    $q->where('collection_name', 'main_image')
                      ->whereNotNull('disk');
                })
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        // Collections
        $collections = cache()->remember('home_collections', 1800, function () {
            return Collection::withCount(['products' => function ($q) {
                    $q->where('active', true);
                }])
                ->where('active', true)
                ->take(4)
                ->get();
        });

        // Featured Products (ONLY featured + active)
        $featured = cache()->remember('home_featured_products', 900, function () {
            return Product::with(['category:id,name,slug', 'media' => function ($q) {
                    $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                      ->where('collection_name', 'main_image')
                      ->whereNotNull('disk')
                      ->limit(1);
                }])
                ->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured', 'created_at')
                ->where('active', true)
                ->where('featured', true)
                ->latest('created_at')
                ->take(8)
                ->get();
        });

        return view('home', compact('title', 'categories', 'recent', 'collections', 'featured'));
    }


   public function thankyou()
   {

    $title = 'WorkFit|Thank you';
       return view('thankyou',compact('title'));
   }


   public function terms()
   {
    $title = 'WorkFit|Terms & Conditions';
       return view( 'terms',compact('title'));
   }


   public function privacy()
   {
    $title = 'WorkFit|Privacy Policy';
       return view( 'privacy',compact('title'));
   }
   public function about()
   {
    $title = 'WorkFit|About Us';
       return view('about',compact('title'));
   }

    public function return()
    {
        $title = 'WorkFit|Return Policy';
        return view('return',compact('title'));
    }
    public function location()
    {
        $title = 'WorkFit|Locations';
        return view( 'location',compact('title'));
    }



}
