<?php

namespace App\Notifications;

use App\Helpdesk\TicketThreadType;
use App\Helpers\AclHelper;
use App\Helpers\ConfigHelper;
use App\Models\NotificationSetting;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public TicketThread $comment;
    public Ticket $ticket;
    public TicketThreadType $threadType;
    public string $notificationSubject = '';
    public string $notificationMessage = '';
    public string $url;
    public bool $isAdmin = false;

    /**
     * Create a new notification instance.
     */
    public function __construct(TicketThread $comment)
    {
        $this->comment = $comment;
        $this->ticket = $this->comment->ticket;
        $this->threadType = TicketThreadType::tryFrom($this->comment->type);
        $this->notificationSubject = $this->makeNotificationSubject();
        $this->notificationMessage = __('mail.ticket.comment.common_text', ['name' => $this->ticket->subject]);
    }

    /**
     * @return string
     */
    private function makeNotificationSubject(): string
    {
        return match ($this->threadType) {
            TicketThreadType::APPROVE_COMMENT => __('mail.ticket.comment.approve', ['id' => $this->ticket->id]),
            TicketThreadType::DECLINE_COMMENT => __('mail.ticket.comment.decline', ['id' => $this->ticket->id]),
            TicketThreadType::SOLVED_COMMENT => __('mail.ticket.comment.solution', ['id' => $this->ticket->id]),
            TicketThreadType::CLOSE_COMMENT => __('mail.ticket.comment.close', ['id' => $this->ticket->id]),
            TicketThreadType::REOPEN_COMMENT => __('mail.ticket.comment.reopen', ['id' => $this->ticket->id]),
            default => __('mail.ticket.comment.new', ['id' => $this->ticket->id]),
        };
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
        /** @var User $notifiable */
        $this->isAdmin = $notifiable->is_admin && AclHelper::adminBelongsToDepartment($this->ticket->department_id);
        $this->url = $this->isAdmin
            ? url('/#/admin/tickets/' . $this->ticket->id)
            : url('/#/user/tickets/' . $this->ticket->id);
        return (new MailMessage)
            ->subject($this->notificationSubject)
            ->theme('default')
            ->view('mail.new_comment', ['comment' => $this->comment, 'ticket' => $this->ticket]);
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
            'title' => $this->notificationSubject,
            'text' => $this->notificationMessage,
            'comment' => $this->comment,
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
        return 'notification.comment.new';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment' => $this->comment->toArray()
        ];
    }
}
