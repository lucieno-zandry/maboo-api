<?php

namespace App\Listeners;

use App\Events\OrderTransactionEvent;
use App\Models\CartItem;
use App\Models\Transaction;

class HandleOrderStock
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    private function updateStock(CartItem $cartItem): int
    {
        $variant = $cartItem->product_variant;
        $product = $cartItem->product;
        $newStock = 0;

        if ($variant) {
            $newStock = $variant->inStock - $cartItem->quantity;

            $newProductVariantData = [
                'inStock' => $newStock,
            ];

            $variant->update($newProductVariantData);
        } else {
            $newStock = $product->inStock - $cartItem->quantity;
            $newProductData = [
                'inStock' => $newStock,
            ];

            $product->update($newProductData);
        }

        return $newStock;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderTransactionEvent $event): void
    {
        if ($event->status === Transaction::STATUS_SUCCESS) {
            $order = $event->order;
            $orderItems = $order->order_items;

            foreach ($orderItems as $orderItem) {
                /** @var CartItem $cartItem */
                $cartItem = $orderItem->cart_item;
                $this->updateStock($cartItem);
            }
        }
    }
}