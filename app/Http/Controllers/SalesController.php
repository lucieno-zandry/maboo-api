<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatuses;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function total(): JsonResponse
    {
        $userType = auth()->user()->type;
        
        $query = DB::table('order_items')
            ->selectRaw("SUM(cart_items.subtotal) as total")
            ->join('cart_items', 'cart_items.id', 'order_items.cart_item_id')
            ->join('users', 'users.id', 'order_items.merchant_id')
            ->where('order_items.status', '!=', OrderStatuses::CANCELLED);

        $query = $userType === User::TYPE_ADMIN ?
            $query->where('users.type', $userType) :
            $query->where('users.id', auth()->id());

        $total = $query->first()->total ?: 0;

        return response()->json(['total' => $total]);
    }
}
