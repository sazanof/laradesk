<?php

namespace App\Listeners;

use App\Events\NewTicket;
use App\Helpdesk\TicketParticipant;
use App\Helpers\MailRecipients;
use App\Mail\NewTicketMail;
use App\Models\NotificationSetting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

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
        Mail::send(new NewTicketMail($ticket));
    }
}
