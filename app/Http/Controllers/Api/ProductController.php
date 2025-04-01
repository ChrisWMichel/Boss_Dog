<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use Illuminate\Support\Facades\Storage;

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

    // Remove image data from product creation
    $images = $data['images'] ?? [];
    unset($data['images']);
    unset($data['image']); // Remove old single image field if present

    // Create the product without images
    $product = Product::create($data);

    // Process and save multiple images
    if (!empty($images)) {
        $this->saveProductImages($product, $images);
    }

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

    // Handle images separately
    $images = $data['images'] ?? [];
    $deletedImages = $data['deleted_images'] ?? [];
    $imagePositions = $data['image_positions'] ?? [];

    unset($data['images']);
    unset($data['deleted_images']);
    unset($data['image_positions']);
    unset($data['image']); // Remove old single image field if present

    // Update product data
    $product->update($data);

    // Process deleted images
    if (!empty($deletedImages)) {
        foreach ($deletedImages as $imageId) {
            $image = $product->images()->find($imageId);
            if ($image) {
                // Delete the physical file
                $fullPath = public_path($image->path);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                // Delete the database record
                $image->delete();
            }
        }
    }

    // Update image positions
    if (!empty($imagePositions)) {
        foreach ($imagePositions as $id => $position) {
            $product->images()->where('id', $id)->update(['position' => $position]);
        }
    }

    // Add new images
    if (!empty($images)) {
        $this->saveProductImages($product, $images);
    }

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
        // $image_name = time().'_'.$image->getClientOriginalName();
        // $image->storeAs('images/products', $image_name, 'public');
        // return 'storage/images/products/'.$image_name;

        $path = 'images/boss_dog';
        $image_name = time().'_'.$image->getClientOriginalName();
        $fullPath = $path.'/'.$image_name;

        $bucket = env('AWS_BUCKET');

        try {
            $fileContents = file_get_contents($image->getRealPath());

            // Get the S3 client directly to access more detailed error information
            $s3Client = Storage::disk('s3')->getClient();

            try {
                // Use the S3 client directly with explicit bucket name
                $result = $s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $fullPath,
                    'Body'   => $fileContents,
                    'ContentType' => $image->getMimeType()
                ]);

                //Log::info("S3 put successful. RequestId: " . ($result['RequestId'] ?? 'N/A'));

                // Construct the URL manually
                $url = "https://{$bucket}.s3." . env('AWS_DEFAULT_REGION') . ".amazonaws.com/{$fullPath}";

                return $url;
            } catch (\Aws\S3\Exception\S3Exception $e) {
                // This will catch specific AWS S3 exceptions
                Log::error("AWS S3 Exception: " . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("General exception: " . $e->getMessage());
            throw $e;
        }
    }

    private function saveProductImages($product, $images)
    {
        $position = $product->images()->max('position') + 1 ?? 1;
        
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $relativePath = $this->saveImage($image);
                
                $product->images()->create([
                    'path' => $relativePath,
                    'url' => $relativePath,
                    'mime' => $image->getMimeType(),
                    'size' => $image->getSize(),
                    'position' => $position++,
                ]);
            }
        }
    }
}
