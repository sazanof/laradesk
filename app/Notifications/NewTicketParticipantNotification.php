<?php

namespace App\Notifications;

use App\Helpdesk\Participant;
use App\Helpers\AclHelper;
use App\Models\NotificationSetting;
use App\Models\Ticket;
use App\Models\TicketParticipant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketParticipantNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $text;
    public TicketParticipant $participant;
    public User $whoAdded;
    public User $user;
    public Ticket $ticket;
    public bool $isMe = false;
    public string $role;

    /**
     * Create a new notification instance.
     */
    public function __construct(TicketParticipant $participant, User $whoAdded)
    {
        $this->participant = $participant;
        $this->user = $participant->user;
        $this->ticket = $this->participant->ticket;
        $this->whoAdded = $whoAdded;
        $this->role = Participant::roleToString($this->participant->role);
        $this->title = __('mail.ticket.participant.new.title',
            [
                'fullname' => $this->participant->user->full_name,
                'role' => $this->role,
                'subject' => $this->ticket->subject,
            ]
        );
        $this->text = __('mail.ticket.participant.new.text',
            [
                'department' => $this->ticket->department->name,
                'fullname1' => $this->whoAdded->full_name,
                'fullname2' => $this->participant->user->full_name,
                'id' => $this->ticket->id,
                'subject' => $this->ticket->subject,
            ]
        );
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        /** @var User $notifiable */
        $notifications = [];
        if ($this->whoAdded->id !== $notifiable->id) {
            $notifications[] = 'database';
            $notifications[] = 'broadcast';
            if ($this->participant->id === $notifiable->id) {
                $this->isMe = true;
            }
            $isMailEnabled =
                NotificationSetting::emailNotificationsEnabled($notifiable->id) &&
                filter_var($notifiable->email, FILTER_VALIDATE_EMAIL);
            if ($isMailEnabled) {
                $notifications[] = 'mail';
            }
        }

        return $notifications;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->view('mail.new_participant',
                [
                    'title' => $this->title,
                    'text' => $this->text,
                    'participant' => $this->participant,
                    'ticket' => $this->ticket,
                    'isMe' => $this->isMe
                ]);
    }

    /**
     * Get the type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'notification.participant.new';
    }

    /**
     * @param object $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast(object $notifiable)
    {
        /** @var User $notifiable */
        return new BroadcastMessage($this->getData($notifiable));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->getData($notifiable);
    }

    private function getData(object $notifiable)
    {
        /** @var User $notifiable */
        return [
            'created' => now()->format('d.m.Y H:i'),
            'type' => $this->broadcastType(),
            'title' => $this->title,
            'text' => $this->text,
            'ticket' => $this->ticket,
            'belongsToDepartment' => AclHelper::adminBelongsToDepartment($this->ticket->department_id, $notifiable),
            'isAssignee' => AclHelper::isAssignee($this->ticket, $notifiable->id),
            'isRequester' => AclHelper::isRequester($this->ticket, $notifiable->id),
            'isObserver' => AclHelper::isObserver($this->ticket, $notifiable->id),
            'isApproval' => AclHelper::isApproval($this->ticket, $notifiable->id),
        ];
    }
}
