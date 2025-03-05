<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductListResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Requesting products');
        $search = request('search', false);
        $per_page = request('per_page', 10);
        $sortField = request('sortField', 'updated_at');
        $sortDirection = request('sortDirection', 'desc');

        $query = Product::query();
        $query->orderBy($sortField, $sortDirection);
        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }
      
       return ProductListResource::collection($query->paginate($per_page));
  
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        /** @var \Illuminate\Http\UploadFile $image */
        $image = $data['image'] ?? null;
        if ($image) {
            $relativePath = $this->saveImage($image);
            Log::info('Image saved to ' . $relativePath);
            $data['image'] = URL::to(Storage::url($relativePath));
            $data['image_size'] = $image->getSize();
        }

        try {
            $product = Product::create($data);
            return new ProductResource($product);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error creating product'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    private function saveImage($image)
    {
        $path = "images/" . Str::random();
        if(!Storage::exists($path)) {
            Storage::disk('public')->makeDirectory($path, 0755, true);
        }
        if (!Storage::disk('public')->putFileAs($path, $image, $image->getClientOriginalName())) {
            Log::error('Failed to save image');
            return null;
        }
        return $path . '/' . $image->getClientOriginalName();
    }
}
