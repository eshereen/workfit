<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class WishlistController extends BaseController
{
    /**
     * Display the user's wishlist
     */
    public function index()
    {
        return view('wishlist.index');
    }
}
