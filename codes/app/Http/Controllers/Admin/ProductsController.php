<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function get(Request $request)
    {
        $limit = $request->limit ?: 20;
        $offset = $request->offset ?: 0;

        $products = Product::adminProducts()
            ->limit($limit)
            ->offset($offset)
            ->get();
            
        return response()->json(['products' => $products]);
    }
}
