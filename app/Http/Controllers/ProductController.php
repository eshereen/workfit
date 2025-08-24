<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CountryCurrencyService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $currencyService;

    public function __construct(CountryCurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Display product listing
     */
    public function index()
    {

        $products = Product::with(['category', 'subcategory', 'media'])
            ->where('active', true)
            ->latest()
            ->paginate(12);

        // Get current currency info
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        // Convert product prices to current currency
        $products->getCollection()->transform(function ($product) use ($currencyInfo) {
            if ($product->price) {
                $product->converted_price = $this->currencyService->convertFromUSD($product->price, $currencyInfo['currency_code']);
            }
            if ($product->compare_price && $product->compare_price > 0) {
                $product->converted_compare_price = $this->currencyService->convertFromUSD($product->compare_price, $currencyInfo['currency_code']);
            }
            return $product;
        });

        return view('products.index', compact('products', 'currencyInfo','title'));
    }

    /**
     * Show single product page
     */
    public function show(Product $product)
    {
        // Get current currency info
        $currencyInfo = $this->currencyService->getCurrentCurrencyInfo();

        // Convert product price to current currency
        if ($product->price) {
            $product->converted_price = $this->currencyService->convertFromUSD($product->price, $currencyInfo['currency_code']);
        }
        if ($product->compare_price && $product->compare_price > 0) {
            $product->converted_compare_price = $this->currencyService->convertFromUSD($product->compare_price, $currencyInfo['currency_code']);
        }

        return view('products.show', compact('product', 'currencyInfo'));
    }
}
