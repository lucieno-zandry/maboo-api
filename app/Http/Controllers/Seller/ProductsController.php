<?php

namespace App\Http\Controllers\Seller;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function get(Request $request)
    {
        $limit = $request->limit ?: 20;
        $offset = $request->offset ?: 0;

        $products = Product::where('user_id', auth()->id())
            ->limit($limit)
            ->offset($offset)
            ->get();
            
        return response()->json(['products' => $products]);
    }
}
