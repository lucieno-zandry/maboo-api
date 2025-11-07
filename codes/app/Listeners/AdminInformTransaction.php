<?php

namespace App\Listeners;

use App\Actions\AdminActions;
use App\Events\OrderTransactionEvent;
use App\Notifications\Transaction\AdminTransactionNotification;

class AdminInformTransaction
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
        $notification = new AdminTransactionNotification();

        $notification
            ->setCustomer($event->order->user)
            ->setType('ORDER')
            ->setStatus($event->status);

        (new AdminActions)->notify($notification);
    }
}