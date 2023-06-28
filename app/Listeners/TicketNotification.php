<?php

namespace App\Listeners;

use App\Events\NewTicket;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TicketNotification
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
    public function handle(NewTicket $event): void
    {
        /** @var Ticket $ticket */
        $ticket = $event->ticket;
        //dump(__CLASS__, $event);
    }
}
