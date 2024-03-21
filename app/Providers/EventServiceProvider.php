<?php

namespace App\Providers;

use App\Events\NewComment;
use App\Events\NewParticipant;
use App\Events\NewTicketEvent;
use App\Listeners\NewCommentNotification;
use App\Listeners\NewParticipantNotification;
use App\Listeners\NewTicketEventListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NewTicketEvent::class => [
            NewTicketEventListener::class
        ],
        NewComment::class => [
            NewCommentNotification::class
        ],
        NewParticipant::class => [
            NewParticipantNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
