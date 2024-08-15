<?php

namespace App\Notifications;

use App\Models\News;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class NewsNotification extends Notification
{
    use Queueable;

    public News $new;

    /**
     * Create a new notification instance.
     */
    public function __construct(News $new)
    {
        $this->new = $new;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        /** @var User $notifiable */
        if (!$this->new->exists()) {
            return [];
        }
        $existing = DB::table('notifications')
            ->where('type', 'App\Notifications\NewsNotification')
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $notifiable->id)
            ->whereRaw("JSON_EXTRACT(`data`, '$.id') = ?", [$this->new->id])
            ->exists();
        if ($existing) {
            return [];
        }
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->new->id,
            'created_at' => $this->new->created_at,
            'title' => $this->new->title,
            'text' => $this->new->text
        ];
    }
}
