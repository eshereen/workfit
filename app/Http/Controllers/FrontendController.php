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

      $products = Product::with(['variants', 'category','subcategory'])
        ->where('active', true)->get();
       return view('home',compact('products'));
   }



   public function thankyou()
   {
       return view(view: 'thankyou');
   }


}
