<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatuses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\MakeOrderRequest;
use App\Models\Order;
use App\Actions\CartActions;
use App\Actions\OrderActions;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    public function delete(Order $order): JsonResponse
    {
        $deleted = false;

        if ($order) {
            $deleted = !$order->transaction_id ? $order->forceDelete() : $order->delete();
        }

        return response()->json(['deleted' => $deleted]);
    }

    public function make(MakeOrderRequest $request, CartActions $cartActions, OrderActions $orderActions)
    {
        $cartItemIds = $request->cart_item_ids;
        $cartItems = $cartActions->getCartItems($cartItemIds);

        if (!$cartItems->isEmpty()) {
            $totalPrice = $cartItems->sum(fn($cartItem) => $cartItem->subtotal);
            $orderId = uuid_create();

            Order::create([
                'id' => $orderId,
                'total_price' => $totalPrice,
                'user_id' => Auth::id(),
            ]);

            $orderActions->createOrderItems($cartItemIds, $orderId);

            return response()->json(['order_id' => $orderId]);
        }
    }

    public function all(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())->latest('created_at')->get();
        return response()->json(['orders' => $orders]);
    }

    public function cancelled(): JsonResponse
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('transaction_id', null)
            ->orWhereHas('transaction', function ($query) {
                $query->where('status', null);
            })
            ->latest()
            ->get();

        return response()->json(['orders' => $orders]);
    }

    public function processing(): JsonResponse
    {
        $orderItems = OrderItem::where('user_id', Auth::id())
            ->whereStatus(OrderStatuses::PROCESSING)
            ->get();

        return response()->json(['order_items' => $orderItems]);
    }

    public function delivered(): JsonResponse
    {
        $orderItems = OrderItem::where('user_id', Auth::id())
            ->whereStatus(OrderStatuses::DELIVERED)
            ->get();

        return response()->json(['order_items' => $orderItems]);
    }
}