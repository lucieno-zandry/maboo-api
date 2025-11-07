<?php

namespace App\Listeners;

use App\Enums\OrderStatuses;
use App\Events\OrderTransactionEvent;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Transaction;

class HandleOrderStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderTransactionEvent $event): void
    {
        if ($event->status === Transaction::STATUS_SUCCESS) {
            $status = OrderStatuses::PROCESSING->value;
            $event->order->order_items->each(function (OrderItem $orderItem) use ($status) {
                OrderStatusHistory::create([
                    'order_item_id' => $orderItem->id,
                    'status' => $status,
                ]);

                $orderItem->status = $status;
                $orderItem->save();
            });
        }
    }
}