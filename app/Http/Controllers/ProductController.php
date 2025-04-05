<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;


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

    public function category(Category $category){
        
        $categories = Category::getAllChildrenByParent($category);

        $products = Product::with('images')
            ->select('products.*')
            ->distinct()
            ->join('product_categories AS pc', 'pc.product_id', '=', 'products.id')
            ->whereIn('pc.category_id', array_map(fn($c) => $c->id, $categories))
            ->where('published', true)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('product.index', ['products' => $products]);
    }
}
