<?php

namespace App\Events;

use App\Models\TicketThread;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewComment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TicketThread $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(TicketThread $comment)
    {
        $this->comment = $comment;
    }
}
