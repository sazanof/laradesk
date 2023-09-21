<?php

namespace App\Listeners;

use App\Events\NewComment;
use App\Helpers\MailRecipients;
use App\Mail\NewTicketComment;
use App\Models\TicketThread;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewCommentNotification
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
    public function handle(NewComment $event): void
    {
        $comment = $event->comment;
        $recipients = MailRecipients::commentAddresses($comment);
        if (!empty($recipients)) {
            Mail
                ::to($recipients)
                ->queue(new NewTicketComment($comment));
        }

    }
}
