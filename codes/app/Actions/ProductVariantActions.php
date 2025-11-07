<?php

namespace App\Actions;
use Illuminate\Http\UploadedFile;

class ProductVariantActions
{
    public function storeImage(UploadedFile $image): string
    {
        $path = $image->store('public/images/product_variants/');
        $path = str_replace('public/', '', $path);

        return $path;
    }
}