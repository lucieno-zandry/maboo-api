<?php

namespace App\Listeners;

use App\Events\OrderTransactionEvent;
use App\Notifications\Transaction\CustomerTransactionNotification;
use Illuminate\Support\Facades\Notification;

class CustomerInformTransaction
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
        $notification = new CustomerTransactionNotification();
        $notification
            ->setStatus($event->status)
            ->setOrderId($event->order->id);

        Notification::send($event->order->user, $notification);
    }
}