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

      $products = Product::with(['variants', 'category','subcategory','media'])
        ->where('active', true)
        ->where('featured', true)
        ->get()
        ->take(12);
       return view('home',compact('products'));
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
