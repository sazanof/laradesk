<?php

namespace App\Listeners;

use App\Events\NewParticipant;
use App\Mail\NewTicketParticipantMail;
use App\Models\Ticket;
use App\Models\TicketParticipants;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NewParticipantNotification
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
    public function handle(NewParticipant $event): void
    {
        foreach ($event->participants as $participant) {
            Mail::queue(new NewTicketParticipantMail($participant, $event->ticket));
        }

    }
}
