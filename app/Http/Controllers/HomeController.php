<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Store;

class HomeController extends Controller
{
    private $product;
    private const QTD_PRODUCTS_HOME = 6;
    private const QTD_STORES_HOME = 3;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index()
    {
        $products = $this->product->limit(self::QTD_PRODUCTS_HOME)->orderBy('id', 'DESC')->get();
        $stores= Store::limit(self::QTD_STORES_HOME)->orderBy('id', 'DESC')->get();

        return view('welcome', compact('products', 'stores'));
    }

    public function single($slug)
    {
        $product = $this->product->whereSlug($slug)->first();

        return view('single', compact('product'));
    }
}
