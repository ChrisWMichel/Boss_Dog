<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images')
            ->orderBy('updated_at', 'desc')
            ->where('published', true)
            ->paginate(10);
        return view('product.index', ['products' => $products]);
    }

    public function show(Product $product)
    {
        // Eager load images
        $product->load('images');

        // Get all image URLs for the product
        $imageUrls = $product->images->pluck('url')->toArray();

        return view('product.view', [
            'product' => $product,
            'imageUrls' => $imageUrls
        ]);
    }
}
