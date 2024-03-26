<?php

namespace App\Mail;

use App\Helpers\MailRecipients;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestUserInfoUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $messageText = '';

    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $message)
    {
        $this->user = $user;
        $this->messageText = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: MailRecipients::superAdministrators(),
            subject: __('mail.request.title', ['username' => $this->user->username])
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
