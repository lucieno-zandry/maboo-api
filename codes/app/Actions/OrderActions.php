<?php

namespace App\Actions;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class OrderActions extends Actions
{

    public function getUser(string $order_id)
    {
        $order = Order::with('user')->find($order_id);
        return $order->user;
    }

    protected function orderCartItems(array $cartItemIds): bool
    {
        return CartItem::whereIn('id', $cartItemIds)
            ->update([
                'ordered_at' => Helpers::getIsoString(date_create())
            ]);
    }

    protected function toFormatedOrderItemData(CartItem $cartItem, string $orderId)
    {
        return [
            'cart_item_id' => $cartItem->id,
            'order_id' => $orderId,
            'user_id' => $cartItem->user_id,
            'merchant_id' => $cartItem->product->user_id,
        ];
    }

    public function createOrderItems(array $cartItemIds, string $orderId): bool
    {
        $orderItemsData = [];

        $cartItems = CartItem::whereIn('id', $cartItemIds)->get();
        if (!$cartItems->isEmpty()) {
            foreach ($cartItems as $cartItem) {
                $orderItemsData[] = $this->toFormatedOrderItemData($cartItem, $orderId);
            }
        }

        $this->orderCartItems($cartItemIds);
        return OrderItem::insert($orderItemsData);
    }

    public function groupedOrderItems(Order $order): array
    {
        $grouped = [];
        $orderItems = $order->order_items;

        $adminId = null;

        foreach ($orderItems as $orderItem) {
            $cartItem = $orderItem->cart_item;

            if ($cartItem?->product && $cartItem->product?->merchant) {
                $product = $cartItem->product;
                $merchant = $product->merchant;

                $merchant_id = $merchant->id;

                if ($merchant->type === User::TYPE_ADMIN) {
                    if (!$adminId) {
                        $adminId = $merchant->id;
                    }

                    $merchant_id = $adminId;
                }

                if (isset($grouped[$merchant_id])) {
                    $grouped[$merchant_id]["orderItems"][] = $orderItem;
                } else {
                    $grouped[$merchant_id] = [
                        'merchant' => $merchant,
                        'orderItems' => [$orderItem],
                    ];
                }
            }
        }

        return array_values($grouped);
    }

    protected function getOrderTotalPrice(\Illuminate\Support\Collection $orderItems): int
    {
        $sum = 0;
        $cartActions = new CartActions();

        foreach ($orderItems as $orderItem) {
            $cartItem = $orderItem->cart_item;
            $product = $cartItem->product;

            $subTotal = $cartActions->getProductPrice($product, [
                'quantity' => $cartItem->quantity,
                'product_variant_id' => $cartItem->product_variant_id,
            ]);
            $sum += $subTotal;
        }

        return $sum;
    }

    protected function getInStock(CartItem $cartItem): int
    {
        $inStock = 0;

        if ($cartItem->product_variant) {
            $inStock = $cartItem->product_variant->inStock;
        } else {
            $inStock = $cartItem->product->inStock;
        }

        return $inStock;
    }

    public function quantityIsAvailable(CartItem $cartItem)
    {
        $quantity = $cartItem->quantity;
        $inStock = $this->getInStock($cartItem);

        return $quantity <= $inStock;
    }

    protected function filterOrderItems(Collection $orderItems): Collection
    {
        $filtered = new Collection;

        foreach ($orderItems as $orderItem) {
            $cartItem = $orderItem->cart_item;

            if (
                !$cartItem ||
                !$cartItem->product ||
                !$this->quantityIsAvailable($cartItem)
            ) {
                $orderItem->delete();
            } else {
                $filtered->add($orderItem);
            }
        }

        return $filtered;
    }

    public function getRefreshedOrder(string $order_id): Order
    {
        $order = Order::find($order_id);

        $filteredOrderItems = $this->filterOrderItems($order->order_items);
        $newTotal = $this->getOrderTotalPrice($filteredOrderItems);

        if ($order->total_price !== $newTotal) {
            $order->total_price = $newTotal;
            $order->save();
        }

        if ($filteredOrderItems->count() !== $order->order_items->count()) {
            unset($order->order_items);
            $order->order_items = $filteredOrderItems;
        }

        return $order;
    }

    public function soldItemsData(Order $order): array
    {
        $soldItemsData = [];

        foreach ($order->order_items as $order_item) {
            /** @var CartItem */
            $cart_item = $order_item->cart_item;

            $soldItemsData[] = [
                'buyer_id' => $order->user_id,
                'product_id' => $cart_item->product_id,
                'merchant_id' => $cart_item->product->user_id,
                'product_variant_id' => $cart_item->product_variant_id,
                'order_id' => $order->id,
                'quantity' => $cart_item->quantity,
                'subtotal' => $cart_item->subtotal,
            ];
        }

        return $soldItemsData;
    }
}