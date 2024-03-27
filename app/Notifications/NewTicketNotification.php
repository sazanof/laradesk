<?php

namespace App\Notifications;

use App\Helpers\AclHelper;
use App\Models\NotificationSetting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketNotification extends Notification
{
    use Queueable;

    public Ticket $ticket;
    public string $title;
    public string $text;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->title = __('mail.ticket.new.title', [
            'subject' => $ticket->subject,
            'id' => $ticket->id
        ]);
        $this->text = __('mail.ticket.new.text', [
            'department' => $ticket->department->name,
            'fullname' => $ticket->requester->full_name,
            'subject' => $ticket->subject,
            'category' => $ticket->category->name,
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $notifications = [];
        $notifications[] = 'database';
        $notifications[] = 'broadcast';
        /** @var User $notifiable */
        $isMailEnabled =
            NotificationSetting::emailNotificationsEnabled($notifiable->id) &&
            filter_var($notifiable->email, FILTER_VALIDATE_EMAIL);
        if ($isMailEnabled) {
            $notifications[] = 'mail';
        }
        return $notifications;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('mail.new_ticket', [
                'ticket' => $this->ticket,
                'title' => $this->title,
                'text' => $this->text,
            ])
            ->subject($this->title);
    }

    /**
     * @param object $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast(object $notifiable)
    {
        /** @var User $notifiable */
        return new BroadcastMessage([
            'created' => now()->format('d.m.Y H:i'),
            'title' => $this->title,
            'text' => $this->text,
            'ticket' => $this->ticket,
            'belongsToDepartment' => AclHelper::adminBelongsToDepartment($this->ticket->department_id, $notifiable),
            'isAssignee' => AclHelper::isAssignee($this->ticket, $notifiable->id),
            'isRequester' => AclHelper::isRequester($this->ticket, $notifiable->id),
            'isObserver' => AclHelper::isObserver($this->ticket, $notifiable->id),
            'isApproval' => AclHelper::isApproval($this->ticket, $notifiable->id),
        ]);
    }

    /**
     * Get the type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'notification.ticket.new';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
