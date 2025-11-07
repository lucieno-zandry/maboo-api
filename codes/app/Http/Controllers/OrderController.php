<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatuses;
use App\Http\Requests\Order\UpdateStatusRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Actions\OrderActions;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{

    public function get(string $id, OrderActions $orderActions)
    {
        $order = $orderActions->getRefreshedOrder($id);

        if ($order->transaction?->status === Transaction::STATUS_SUCCESS)
            abort(403);

        return response()->json(['order' => $order]);
    }

    public function grouped(Order $order, OrderActions $orderActions)
    {
        $grouped = $orderActions->groupedOrderItems($order);
        return response()->json(['grouped' => $grouped]);
    }

    public function updateStatus(UpdateStatusRequest $request)
    {
        $data = $request->validated();
        $updated = OrderItem::whereIn('id', $data['ids'])->update(['status' => $data['status']]);
        return response()->json(['updated' => $updated]);
    }

    public function processing(): JsonResponse
    {
        $orderItems = OrderItem::with('user');

        if (auth()->user()->type === User::TYPE_ADMIN){
            $orderItems = $orderItems->adminItems();
        }else{
            $orderItems = $orderItems->where('merchant_id', auth()->id());
        }

        $orderItems = $orderItems
            ->whereStatus(OrderStatuses::PROCESSING)
            ->get();

        return response()->json(['orders' => $orderItems]);
    }

    public function delivered(): JsonResponse
    {
        $orderItems = OrderItem::with('user');

        if (auth()->user()->type === User::TYPE_ADMIN){
            $orderItems = $orderItems->adminItems();
        }else{
            $orderItems = $orderItems->where('merchant_id', auth()->id());
        }

        $orderItems = $orderItems
            ->whereStatus(OrderStatuses::DELIVERED)
            ->get();

        return response()->Json([
            'orders' => $orderItems
        ]);
    }
}