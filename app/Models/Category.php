<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasSlug;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [ 'name', 'slug', 'parent_id', 'active'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public static function getActiveAsTree($resourceClassName = null)
    {
        $categories = Category::where('active', true)->orderBy('parent_id')->get();
        return self::buildCategoryTree($categories, null, $resourceClassName);

        Log::info('Category tree result type: ' . gettype($result));
        Log::info('Category tree result: ' . json_encode($result));
    }

    public static function getAllChildrenByParent(Category $category)
    {
        $categories = Category::where('active', true)->orderBy('parent_id')->get();
        $result[] = $category;
        self::getCategoriesArray($categories, $category->id, $result);

        return $result;
    }

    private static function buildCategoryTree($categories, $parentId = null, $resourceClassName = null)
    {
        $categoryTree = [];

        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $children = self::buildCategoryTree($categories, $category->id, $resourceClassName);
                if ($children) {
                    $category->setAttribute('children', $children);
                }
                $categoryTree[] = $resourceClassName ? new $resourceClassName($category) : $category;
            }
        }

        return $categoryTree;
    }

    private static function getCategoriesArray($categories, $parentId, &$result)
    {
        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $result[] = $category;
                self::getCategoriesArray($categories, $category->id, $result);
            }
        }
    }
}