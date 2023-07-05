<?php

namespace App\Listeners;

use App\Events\NewComment;
use App\Models\TicketThread;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        /** @var TicketThread $comment */
        $comment = $event->comment;
        dd($comment);
    }
}
