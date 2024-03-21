<?php

namespace App\Listeners;

use App\Events\NewTicketEvent;
use App\Events\NewTicketToUserEvent;
use App\Helpdesk\TicketParticipant;
use App\Helpdesk\WebsocketsNotification;
use App\Helpers\MailRecipients;
use App\Mail\NewTicketMail;
use App\Mail\NewTicketParticipantMail;
use Illuminate\Support\Facades\Mail;

class NewTicketEventListener
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
     * @throws \Exception
     */
    public function handle(NewTicketEvent $event): void
    {
        $ticket = $event->ticket;
        // Send Email
        /*Mail::queue(new NewTicketMail($ticket));
        // Send email too new participants
        if (!empty($ticket->approvals)) {
            foreach ($ticket->approvals as $approval) {
                $recipient = MailRecipients::single($approval);
                if (!empty($recipient)) {
                    Mail
                        ::to($recipient)
                        ->queue(new NewTicketParticipantMail($approval, $ticket));
                }
            }
        }
        if (!empty($ticket->observers)) {
            foreach ($ticket->observers as $observer) {
                $recipient = MailRecipients::single($observer);
                if (!empty($recipient)) {
                    Mail
                        ::to($recipient)
                        ->queue(new NewTicketParticipantMail($observer, $ticket));
                }
            }
        }*/
        // Notification all administrators vie sending broadcast message to channel
        $departmentMembers = TicketParticipant::getAdministrators($ticket->department_id);

        dump($departmentMembers);

        foreach ($departmentMembers as $member) {
            NewTicketToUserEvent::broadcast($ticket, $member);
        }

    }
}
