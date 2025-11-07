<?php

namespace App\Listeners\OrderConfirmed;

use App\Events\Transaction\OrderConfirmedEvent;
use App\Models\CartItem;

class HandleStock
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
    public function handle(OrderConfirmedEvent $event): void
    {
        $order = $event->order;
        $orderItems = $order->order_items;

        foreach ($orderItems as $orderItem) {
            /** @var CartItem $cartItem */
            $cartItem = $orderItem->cart_item;
            $this->updateStock($cartItem);
        }
    }
}