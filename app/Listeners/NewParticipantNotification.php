<?php

namespace App\Listeners;

use App\Events\NewParticipant;
use App\Helpers\MailRecipients;
use App\Mail\NewTicketParticipantMail;
use App\Models\NotificationSetting;
use App\Models\Ticket;
use App\Models\TicketParticipant;
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
        /** @var TicketParticipant $participant */
        foreach ($event->participants as $participant) {
            $p = MailRecipients::single($participant);
            if (!empty($p) && NotificationSetting::emailNotificationsEnabled($participant->user_id)) {
                Mail::to($p)->queue(new NewTicketParticipantMail($participant, $event->ticket));
            }
        }
    }
}
