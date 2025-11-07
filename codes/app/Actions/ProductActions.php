<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\ProductImage;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class ProductActions
{
    public function storeImage(UploadedFile $uploadedFile)
    {
        $path = $uploadedFile->store('public/images/product');
        return str_replace('public/', '', $path);
    }

    public function getProduct(int $id)
    {
        $product = Product::find($id);
        return $product;
    }

    public function getInsertImagesData($images, int $product_id)
    {
        $insertImagesData = [];

        foreach ($images as $image) {
            $path = $this->storeImage($image);
            $insertImagesData[] = [
                'product_id' => $product_id,
                'name' => $path
            ];
        }

        return $insertImagesData;
    }

    public function deleteImages(Collection $images): array
    {
        $deleted = [];

        foreach ($images as $image) {
            Helpers::deleteImage($image->name);
            $deleted[] = $image->id;
            $image->forceDelete();
        }

        return $deleted;
    }

    public function getTrashedImages(int $product_id): ProductImage|Collection
    {
        $trashedImages = ProductImage::where('product_id', $product_id)
            ->withTrashed()
            ->where('deleted_at', '!=', null)
            ->get();

        return $trashedImages;
    }

    public function deleteTrashedImages(int $product_id): int
    {
        $trashedImages = $this->getTrashedImages($product_id);
        $affected = 0;

        if ($trashedImages->count() > 0) {
            foreach ($trashedImages as $trashedImage) {
                Helpers::deleteImage($trashedImage->name);
                $trashedImage->forceDelete();
                $affected++;
            }
        }

        return $affected;
    }

    public function restoreTrashedImages(int $product_id): bool
    {
        $restored = false;
        $trashedImages = $this->getTrashedImages($product_id);

        if ($trashedImages->count() > 0) {
            foreach ($trashedImages as $trashedImage) {
                $trashedImage->restore();
                $restored = true;
            }
        }

        return $restored;
    }

    public function getProductsOf(?int $userId, ?string $userType): Collection
    {
        $products = Product::Select('products.*')
            ->join('users', 'users.id', '=', 'products.user_id');

        if ($userId) {
            $products = $products->where('user_id', $userId);
        }

        if ($userType) {
            $products = $products->where('users.type', $userType);
        }

        return $products->get();
    }
}