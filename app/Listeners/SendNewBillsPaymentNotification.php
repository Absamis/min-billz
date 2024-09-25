<?php

namespace App\Listeners;

use App\Notifications\BillsPaymentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNewBillsPaymentNotification
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
    public function handle(object $event): void
    {
        //
        $user = auth()->user();
        $transaction = $event->transaction;
        Notification::send($user, new BillsPaymentNotification($transaction));
    }
}
