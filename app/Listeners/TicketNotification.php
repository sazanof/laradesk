<?php

namespace App\Listeners;

use App\Events\NewTicket;
use App\Helpdesk\TicketParticipant;
use App\Helpers\MailRecipients;
use App\Mail\NewTicketApproval;
use App\Mail\NewTicketMail;
use App\Mail\NewTicketParticipantMail;
use App\Models\NotificationSetting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
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
        $ticket = $event->ticket;
        // Send Email
        Mail::queue(new NewTicketMail($ticket));
        // Send email too new participants
        if (!empty($ticket->approvals)) {
            foreach ($ticket->approvals as $approval) {
                Mail::queue(new NewTicketParticipantMail($approval));
            }
        }
        if (!empty($ticket->observers)) {
            foreach ($ticket->observers as $observer) {
                Mail::queue(new NewTicketParticipantMail($observer));
            }
        }

    }
}
