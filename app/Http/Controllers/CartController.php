<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        //session()->forget('cart');
        $cart = session()->has('cart') ? session()->get('cart') : [];

        return view('cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $productData = $request->get('product');

        $product = Product::whereSlug($productData['slug']);

        if (!$product->count() || $productData['amount'] == 0)
            return redirect()->route('home');

        $product = array_merge($productData, $product->first(['name', 'price'])->toArray());

        if (session()->has('cart')) {

            $products = session()->get('cart');
            $productsSlug = array_column($products, 'slug');

            if (in_array($product['slug'], $productsSlug)) {
                $products = $this->productIncrement($product['slug'], $product['amount'], $products);
                session()->put('cart', $products);
            } else {
                session()->push('cart', $product);
            }

        } else {
            $products[] = $product;
            session()->put('cart', $products);
        }

        flash('Produto Adicionado no Carrinho!')->success();
        return redirect()->route('product.single', ['slug' => $product['slug']]);
    }

    public function remove($slug)
    {
        if (!session()->has('cart'))
            return redirect()->route('cart.index');

        $products = session()->get('cart');

        $products = array_filter($products, function($product) use($slug){
            return $product['slug'] != $slug;
        });

        session()->put('cart', $products);

        flash('Produto Removido do Carrinho de Compras!')->success();
        return redirect()->route('cart.index');
    }

    public function cancel()
    {
        session()->forget('cart');

        flash('Compra Cancelada com Sucesso!')->success();
        return redirect()->route('cart.index');
    }

    private function productIncrement($slug, $amount, $products)
    {
        $products = array_map(function($product) use($slug, $amount){
            if ($slug == $product['slug']) {
                $product['amount'] += $amount;
            }
            return $product;
        }, $products);

        return $products;
    }
}
