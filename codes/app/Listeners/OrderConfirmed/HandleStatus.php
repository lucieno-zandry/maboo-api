<?php

namespace App\Listeners\OrderConfirmed;

use App\Enums\OrderStatuses;
use App\Events\Transaction\OrderConfirmedEvent;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleStatus
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
    public function handle(OrderConfirmedEvent $event): void
    {
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