<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductListResource;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

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
            ->with('images')
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
        // Eager load the images relationship
        $product->load('images');
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
    $images = [];
    $noNewImages = $request->has('no_new_images');

    // Check if we have images in the request files
    if ($request->hasFile('images')) {
        $images = $request->file('images');
    } elseif (isset($data['images']) && is_array($data['images']) && !$noNewImages) {
        $images = $data['images'];
    }

    $deletedImages = $data['deleted_images'] ?? [];

    $imagePositions = $data['image_positions'] ?? [];

    unset($data['images']);
    unset($data['deleted_images']);
    unset($data['image_positions']);
    unset($data['image']); // Remove old single image field if present

    $product->update($data);

    // Process deleted images
    if (!empty($deletedImages)) {

        $s3 = new S3Client([
            'region' => 'us-east-2',
            'version' => 'latest',
            'credentials' => [
                'key'    => config('app.AWS_ACCESS_KEY_ID'),
                'secret' => config('app.AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
        $bucketName = 'images-cwm-portfolio';
        $objectKey = 'images/boss_dog';

        foreach ($deletedImages as $imageInfo) {
            // Check if $imageInfo is an object with id and filename
            if (is_array($imageInfo) && isset($imageInfo['id']) && isset($imageInfo['filename'])) {
                $imageId = $imageInfo['id'];
                $filename = $imageInfo['filename'];

                // Try to find the image by ID
                $image = $product->images()->find($imageId);

                if ($image) {
                    Log::info("Found image by ID: " . $image->id);

                    // Delete the image from S3
                    try {
                        $path = parse_url($image->url, PHP_URL_PATH);
                        $path = ltrim($path, '/');

                        $result = $s3->deleteObject([
                            'Bucket' => $bucketName,
                            'Key' => $objectKey . '/' . $filename
                        ]);

                    } catch (AwsException $e) {
                        Log::warning("Failed to delete S3 image but continuing: " . $e->getMessage());
                    }

                    // Delete the database record
                    $image->delete();
                } else {
                    Log::warning("Image not found by ID: " . $imageId);
                }
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
    if (!empty($images) && !$noNewImages) {
        $this->saveProductImages($product, $images);
    } else {
        Log::info("No new images to process");
    }

    return new ProductResource($product);
}

public function getImageIdByFilename(Request $request)
{
    $filename = $request->input('filename');
    $productId = $request->input('product_id');

    if (!$filename || !$productId) {
        return response()->json(['error' => 'Filename and product_id are required'], 400);
    }

    $product = Product::find($productId);
    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    // Get all images for this product
    $allImages = $product->images()->get();

    foreach ($allImages as $image) {
        // Check if the URL contains the filename
        if (strpos($image->url, $filename) !== false) {
            return response()->json([
                'id' => $image->id,
                'url' => $image->url
            ]);
        }
    }

    return response()->json(['error' => 'Image not found'], 404);
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
                $s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $fullPath,
                    'Body'   => $fileContents,
                    'ContentType' => $image->getMimeType()
                ]);

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

        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                try {
                    $relativePath = $this->saveImage($image);

                    $newImage = $product->images()->create([
                        'path' => $relativePath,
                        'url' => $relativePath,
                        'mime' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'position' => $position++,
                    ]);

                } catch (\Exception $e) {
                    Log::error("Error saving image: " . $e->getMessage());
                }
            } else {
                Log::warning("Image {$index} is not an UploadedFile, skipping");
                Log::warning("Image details: " . json_encode($image));
            }
        }
    }
}


