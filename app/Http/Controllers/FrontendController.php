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
       return view(view: 'thankyou');
   }


   public function terms()
   {
       return view(view: 'terms');
   }


   public function privacy()
   {
       return view(view: 'privacy');
   }


}
