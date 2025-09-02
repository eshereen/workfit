<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
   public function index()
   {
    $title = 'WorkFit|Home';

      // Optimized queries with caching and limits
      $categories = cache()->remember('home_categories', 1800, function () {
          return Category::withCount(['products' => function ($query) {
              $query->where('active', true);
          }])->where('active', true)->take(4)->get();
      });

      // Single featured products query with optimization
      $featured = cache()->remember('home_featured_products', 900, function () {
          return Product::with(['category:id,name,slug', 'media' => function ($query) {
              $query->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                    ->whereIn('collection_name', ['main_image'])
                    ->whereNotNull('disk')
                    ->limit(1);
          }])
          ->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured', 'created_at')
          ->where('active', true)
          ->where('featured', true)
          ->orderBy('created_at', 'desc')
          ->take(8)
          ->get();
      });

      // Optimized category queries with product limits
      $men = cache()->remember('home_men_category', 1800, function () {
          return Category::with(['products' => function ($query) {
              $query->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured')
                    ->where('active', true)
                    ->with(['media' => function ($q) {
                        $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                          ->whereIn('collection_name', ['main_image'])
                          ->whereNotNull('disk')
                          ->limit(1);
                    }])
                    ->take(8);
          }])->where('name', 'Men')->first();
      });

      $women = cache()->remember('home_women_category', 1800, function () {
          return Category::with(['products' => function ($query) {
              $query->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured')
                    ->where('active', true)
                    ->with(['media' => function ($q) {
                        $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                          ->whereIn('collection_name', ['main_image'])
                          ->whereNotNull('disk')
                          ->limit(1);
                    }])
                    ->take(8);
          }])->where('name', 'Women')->first();
      });

      $kids = cache()->remember('home_kids_category', 1800, function () {
          return Category::with(['products' => function ($query) {
              $query->select('id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'active', 'featured')
                    ->where('active', true)
                    ->with(['media' => function ($q) {
                        $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                          ->whereIn('collection_name', ['main_image'])
                          ->whereNotNull('disk')
                          ->limit(1);
                    }])
                    ->take(8);
          }])->where('name', 'Kids')->first();
      });

      return view('home', compact('men', 'women', 'kids', 'featured', 'categories'));


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
