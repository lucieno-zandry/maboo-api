<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariant\CreateProductVariantRequest;
use App\Http\Requests\ProductVariant\UpdateProductVariantRequest;
use App\Models\ProductVariant;
use App\Actions\ProductVariantActions;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class ProductVariantController extends Controller
{
    public function create(CreateProductVariantRequest $request, ProductVariantActions $productVariantActions)
    {
        $data = $request->validated();
        $data['image'] = $productVariantActions->storeImage($request->file('image'));

        $productVariant = ProductVariant::create($data);
        return response()->json(['variant' => $productVariant]);
    }

    public function delete(Request $request)
    {
        $ids = $request->input('ids');
        $variants = ProductVariant::whereIn('id', $ids)->get();

        if (!$variants->isEmpty()) {
            foreach ($variants as $variant) {
                if ($variant->image) {
                    Helpers::deleteImage($variant->image);
                }

                $variant->delete();
            }
        }

        return response()->json(['deleted' => true]);
    }

    public function update(UpdateProductVariantRequest $request, ProductVariantActions $productVariantActions, int $id)
    {
        $productVariant = ProductVariant::find($id);

        if (!$productVariant)
            throw ValidationException::withMessages([
                'id' => "The variant does not exist"
            ]);

        if (!Auth::user()->can('update', $productVariant))
            abort('403');

        $data = $request->only('name', 'price', 'inStock');

        if ($request->hasFile('image')) {
            if ($productVariant->image)
                Helpers::deleteImage($productVariant->image);

            $data['image'] = $productVariantActions->storeImage($request->image);
        }
        
        $productVariant->update($data);
        return response()->json(['variant' => $productVariant]);
    }
}