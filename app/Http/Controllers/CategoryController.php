<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Actions\CategoryActions;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class CategoryController extends Controller
{
    public function create(CategoryCreateRequest $request, CategoryActions $categoryActions)
    {
        $data = $request->validated();

        if (isset($data['parent_id']) && !empty($data['parent_id'])) {
            $parent = Category::find($data['parent_id']);
            $data['level'] = ++$parent->level;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $categoryActions->storeImage($request->image);
        }

        $category = Category::create($data);

        return response()->json(['category' => $category]);
    }

    public function update(CategoryUpdateRequest $request, CategoryActions $categoryActions)
    {
        $data = $request->validated();
        $category = Category::find($request->id);

        if ((isset($request->image) || $request->image === null) && $category->image) {
            Helpers::deleteImage($category->image);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $categoryActions->storeImage($request->image);
        }

        $updated = $category->update($data);
        return response()->json(['updated' => $updated]);
    }

    public function delete(Request $request, CategoryActions $categoryActions)
    {
        $request->validate([
            'ids' => 'array',
        ]);

        $data = $request->all();
        $categoryIds = $data['ids'];

        $deleteIds = [];

        foreach ($categoryIds as $categoryId) {
            $deleteIds[] = $categoryId;
            $categoryActions->allChildrenIds($categoryId, $deleteIds);
        }

        if (!empty($deleteIds)) {
            $deleted = Category::whereIn('id', array_unique($deleteIds))->delete();
        }

        return response()->json(['deleted' => $deleted]);
    }

    public function all()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }

    public function getHierarchy(CategoryActions $categoryActions)
    {
        $hierarchy = $categoryActions->getHierarchy();
        return response()->json(['hierarchy' => $hierarchy]);
    }

    public function products(Request $request)
    {
        $limit = $request->limit ?: 20;
        $offset = $request->offset ?: 0;

        $products = Category::products($request->id)
            ->limit($limit)
            ->offset($offset);

        $products = $products->get();
        return response()->json(['products' => $products]);
    }
}