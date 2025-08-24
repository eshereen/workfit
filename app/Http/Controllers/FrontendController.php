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
      $categories = Category::with(['products'])->get();
      $products = Product::with(['variants', 'category','subcategory','media'])
        ->where('active', true)
        ->where('featured', true)
       ->orderBy('created_at','desc')
       ->take(12);

        $featured = Product::with(['variants', 'category','subcategory','media'])
        ->where('active', true)
        ->where('featured', true)

        ->take(8);

        $men = Category::with(['products'])->where('name','Men')->first();
        $women = Category::with(['products'])->where('name','Women')->first();
        $kids = Category::with(['products'])->where('name','Kids')->first();

        return view('home',compact('products','men','women','kids','featured','categories'));


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
