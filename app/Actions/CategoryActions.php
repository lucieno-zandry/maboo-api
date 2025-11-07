<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
    
class CategoryActions
{
    public function storeImage(UploadedFile $image): string
    {
        $path = $image->store('public/images/categories/');
        $path = str_replace('public/', '', $path);

        return $path;
    }

    public function getHierarchy(?int $id = null): array
    {
        $categories = Category::where('parent_id', $id ?? null)->get();

        $hierarchy = $categories->map(function (Category $category) {
            return [
                'category' => $category,
                'children' => $this->getHierarchy($category->id),
            ];
        })->all();

        return $hierarchy;
    }

    public function allChildrenIds(int $category_id, array &$categoryIds): void
    {
        $categories = Category::where('parent_id', $category_id)->get();

        if (!$categories->isEmpty()) {
            foreach ($categories as $category) {
                $categoryIds[] = $category->id;
                $this->allChildrenIds($category->id, $categoryIds);
            }
        }
    }

}