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
            return Category::where('categories.active', true)
                ->orderBy('name', 'asc') // Order by name for consistency
                ->take(4)
                ->with(['products' => function ($query) {
                    $query->with(['media' => function ($q) {
                            $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                              ->where('collection_name', 'main_image')
                              ->whereNotNull('disk')
                              ->limit(1);
                        }, 'category:id,name,slug', 'subcategory:id,name,slug,category_id'])
                        ->where('products.active', true)
                        ->take(8);
                }])
                ->get();
        });

        // Recent Products
        $recent = cache()->remember('home_recent_products', 1800, function () {
            return Product::select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'created_at')
                ->with([
                    'category:id,name,slug',
                    'media' => function ($query) {
                        $query->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                              ->whereIn('collection_name', ['main_image', 'product_images'])
                              ->whereNotNull('disk');
                    }
                ])
                ->where('products.active', true)
                ->whereHas('media', function ($query) {
                    $query->where('collection_name', 'main_image')
                          ->whereNotNull('disk');
                })
                ->latest('created_at')
                ->take(8)
                ->get();
        });

        // Collections
        $collections = cache()->remember('home_collections', 1800, function () {
            return Collection::withCount(['products' => function ($q) {
                    $q->where('products.active', true); // ✅ fixed
                }])
                ->where('collections.active', true)
                ->take(4)
                ->get();
        });

        // Featured Products
        $featured = cache()->remember('home_featured_products', 900, function () {
            return Product::select('id', 'name', 'slug', 'price', 'compare_price', 'active', 'featured', 'created_at')
            ->with([
                'category:id,name,slug',
                'media' => function ($q) {
                    $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                      ->whereIn('collection_name', ['main_image', 'product_images'])
                      ->whereNotNull('disk');
                }
            ])
            ->where('products.active', true)
            ->where('products.featured', true)
            ->whereHas('media', function ($query) {
                $query->where('collection_name', 'product_images');
            })
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
