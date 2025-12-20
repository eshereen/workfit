<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Subcategory;
use App\Models\Banner;
use App\Services\BestSellerService;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        $title = 'WorkFit|Home';

        // Quick check: if no categories exist, return empty data immediately
        $hasData = cache()->remember('site_has_data', 3600, function () {
            return Category::exists() || Product::exists();
        });

        if (!$hasData) {
            // Return empty collections for all data
            return view('home', [
                'title' => $title,
                'categories' => collect([]),
                'recent' => collect([]),
                'collections' => collect([]),
                'featured' => collect([]),
                'men' => null,
                'women' => null,
                'sale' => null
            ]);
        }

        // Load main categories with their own products only
        try {
            $categories = cache()->remember('home_categories', 1800, function () {
                try {
                    return Category::where('categories.active', true)
                        ->orderBy('categories.name', 'asc')
                        ->take(4)
                        ->with(['directProducts' => function ($query) {
                            $query->with(['media' => function ($q) {
                                    $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                                    ->where('collection_name', ['main_image','product_images']);
                                }, 'category:id,name,slug', 'subcategory:id,name,slug,category_id'])
                                ->where('products.active', true)
                                ->take(8);
                        }])
                        ->get();
                } catch (\Exception $e) {
                    return collect([]);
                }
            });
        } catch (\Exception $e) {
            $categories = collect([]);
        }

        // Men's Category - Get only men's category with its products
        try {
            $men = cache()->remember('home_men_category', 1800, function () {
                try {
                    return Category::where('categories.active', true)
                        ->where('categories.slug', 'men ')  // Exact match for 'men ' (with trailing space)
                        ->with(['directProducts' => function ($query) {
                            $query->with(['media' => function ($q) {
                                    $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                                      ->whereIn('collection_name', ['main_image', 'product_images'])
                                      ->whereNotNull('disk')
                                      ->orderBy('collection_name', 'asc')
                                      ->orderBy('id', 'asc');
                                }, 'category:id,name,slug', 'subcategory:id,name,slug,category_id'])
                                ->where('products.active', true)
                                ->take(8);
                        }])
                        ->first(); // Use first() instead of get() since we only want one category
                } catch (\Exception $e) {
                    return null;
                }
            });
        } catch (\Exception $e) {
            $men = null;
        }


        // Women's Category - Get only women's category with its products
        try {
            $women = cache()->remember('home_women_category', 1800, function () {
                try {
                    return Category::where('categories.active', true)
                        ->where(function($query) {
                            $query->where('categories.slug', 'women')  // Standard

                                  ->orWhere('categories.name', 'LIKE', '%women%')  // Live server
                                  ->orWhere('categories.name', 'LIKE', '%Women%'); // Live server
                        })
                        ->with(['directProducts' => function ($query) {
                            $query->with(['media' => function ($q) {
                                    $q->select('id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk')
                                      ->whereIn('collection_name', ['main_image', 'product_images'])
                                      ->whereNotNull('disk')
                                      ->orderBy('collection_name', 'asc')
                                      ->orderBy('id', 'asc');
                                }, 'category:id,name,slug', 'subcategory:id,name,slug,category_id'])
                                ->where('products.active', true)
                                ->take(8);
                        }])
                        ->first(); // Use first() instead of get() since we only want one category
                } catch (\Exception $e) {
                    return null;
                }
            });
        } catch (\Exception $e) {
            $women = null;
        }

        // Recent Products
        try {
            $recent = cache()->remember('home_recent_products', 1800, function () {
                try {
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
                        ->take(4)
                        ->get();
                } catch (\Exception $e) {
                    return collect([]);
                }
            });
        } catch (\Exception $e) {
            $recent = collect([]);
        }

        // Collections
        try {
            $collections = cache()->remember('home_collections', 1800, function () {
                try {
                    return Collection::withCount(['products' => function ($q) {
                            $q->where('products.active', true); // ✅ fixed
                        }])
                        ->where('collections.active', true)
                        ->take(4)
                        ->get();
                } catch (\Exception $e) {
                    return collect([]);
                }
            });
        } catch (\Exception $e) {
            $collections = collect([]);
        }

        // Featured Products - sorted by most added (best sellers first)
        try {
            $featured = cache()->remember('home_featured_products', 900, function () {
                try {
                    $bestSellerService = app(BestSellerService::class);
                    $bestSellerIds = $bestSellerService->getBestSellingProducts(50);

                    $query = Product::select('id', 'name', 'slug', 'price', 'compare_price', 'active', 'featured', 'created_at')
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
                    });

                    // Sort by best sellers first, then by created_at
                    if (!empty($bestSellerIds)) {
                        $bestSellers = $query->whereIn('id', $bestSellerIds)
                            ->orderByRaw('FIELD(id, ' . implode(',', $bestSellerIds) . ')')
                            ->get();

                        $remaining = $query->whereNotIn('id', $bestSellerIds)
                            ->orderBy('created_at', 'desc')
                            ->get();

                        return $bestSellers->merge($remaining)->take(8);
                    } else {
                        return $query->orderBy('created_at', 'desc')->take(8)->get();
                    }
                } catch (\Exception $e) {
                    return collect([]);
                }
            });
        } catch (\Exception $e) {
            $featured = collect([]);
        }

        // Sale Collection
        try {
            $sale = cache()->remember('home_sale_collection', 1800, function () {
                try {
                    return Collection::where('collections.slug', 'sale')
                        ->where('collections.active', true)
                        ->orderBy('collections.created_at', 'desc')
                        ->first();
                } catch (\Exception $e) {
                    return null;
                }
            });
        } catch (\Exception $e) {
            $sale = null;
        }
        $heroBanner = Banner::getBySection('hero');
        $run = Banner::getBySectionPattern('run')->first();
        $train = Banner::getBySectionPattern('train')->first();
        $rec = Banner::getBySectionPattern('rec')->first();
        $women_banner = Banner::getBySectionPattern('women_banner')->first();
        $group_banner = Banner::getBySectionPattern('group_banner')->first();
        $featured_banner = Banner::getBySectionPattern('featured_banner')->first();

        return view('home', compact('title', 'categories', 'recent', 'collections','heroBanner', 'featured', 'men', 'women', 'sale', 'run', 'train', 'rec', 'women_banner', 'group_banner', 'featured_banner'));
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
