<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sortField = request('sortField', 'name');
        $sortDirection = request('sortDirection', 'asc');

        $categories = Category::query()
        ->with('parent')
        ->when($sortField, function($query) use ($sortField, $sortDirection) {
            return $query->orderBy($sortField, $sortDirection);
        })
        ->get();

        return CategoryResource::collection($categories);
    }

 

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $category = Category::create($data);

        return new CategoryResource($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $category->update($data);

        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
