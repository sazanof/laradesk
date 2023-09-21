<?php

namespace App\Listeners;

use App\Events\NewTicket;
use App\Helpdesk\TicketParticipant;
use App\Helpdesk\WebsocketClient;
use App\Helpdesk\WebsocketsNotification;
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
     * @throws \Exception
     */
    public function handle(NewTicket $event): void
    {
        $ticket = $event->ticket;
        // Send Email
        Mail::queue(new NewTicketMail($ticket));
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
        }
        WebsocketClient::sendNotificationToAdministrators(new WebsocketsNotification([
            'user_id' => null,
            'conn_id' => null,
            'action' => 'new_ticket',
            'text' => __('mail.ticket.new', [
                'id' => $ticket->id,
                'subject' => $ticket->subject
            ])
        ]));
    }
}
