<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sortField', 'updated_at');
        $sortDirection = request('sortDirection', 'asc');

        $query = Product::query()
            ->where('title', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return ProductListResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        /** @var \Illuminate\Http\UploadedFile $image */
        $image = $data['image'] ?? null;
        // Check if image was given and save on local file system
        if ($image) {
            $relativePath = $this->saveImage($image);
            $data['image'] = $relativePath;
            $data['image_size'] = $image->getSize();
        }

        $product = Product::create($data);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product      $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
{
    $data = $request->validated();
    $data['updated_by'] = $request->user()->id;
    
    // Check if image is being updated
    $imageUpdated = $request->input('imageUpdated', false);
    
    /** @var \Illuminate\Http\UploadedFile $image */
    $image = $data['image'] ?? null;

    // Only process image if imageUpdated flag is true
    if ($imageUpdated && $image) {
        // Get the old image before updating
        $oldImage = $product->image;
        
        // Save the new image
        $relativePath = $this->saveImage($image);
        $data['image'] = $relativePath; 
        $data['image_size'] = $image->getSize();

        // Delete the old image if it exists
        if ($oldImage) {
            // Get the full path to the old image
            $fullPath = public_path($oldImage);
            
            if (file_exists($fullPath)) {
                unlink($fullPath);
            } else {
                Log::warning('Image not found: ', ['oldImage' => $oldImage, 'fullPath' => $fullPath]);
            }
        }
    } else {
        unset($data['image']);
    }

    $product->update($data);

    return new ProductResource($product);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }

    private function saveImage(UploadedFile $image)
    {
        $image_name = time().'_'.$image->getClientOriginalName();
        $image->storeAs('images/products', $image_name, 'public');
        return 'storage/images/products/'.$image_name;
    }
}
