<?php

namespace App\Notifications;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $title;
    public string $text;
    public string $filename;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $filename)
    {
        $this->title = __('mail.export.finished.title');
        $this->text = __('mail.export.finished.text');
        $this->filename = $filename;
    }

    /**
     * @param User $notifiable
     * @return string[]
     */
    public function via(User $notifiable): array
    {
        $notifications = [];
        $notifications[] = 'database';
        $notifications[] = 'broadcast';
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
            ->view('mail.export_success', ['filename' => $this->filename])
            ->subject($this->title);
    }

    /**
     * @param User $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'created' => now()->format('d.m.Y H:i'),
            'title' => $this->title,
            'text' => $this->text,
            'filename' => $this->filename,
        ]);
    }

    /**
     * Get the type of the notification being broadcast.
     */
    public function broadcastType(): string
    {
        return 'notification.export.finished';
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
