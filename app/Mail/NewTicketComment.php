<?php

namespace App\Mail;

use App\Helpdesk\TicketThreadType;
use App\Models\Config;
use App\Models\Ticket;
use App\Models\TicketThread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTicketComment extends Mailable
{
    use Queueable, SerializesModels;

    public TicketThread $comment;
    public Ticket $ticket;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct(TicketThread $comment)
    {
        $this->comment = $comment;
        $this->ticket = Ticket::findOrFail($comment->ticket_id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), Config::appName()),
            subject: $this->getSubjectTranslation(),
        );
    }

    /**
     * @return string
     */
    protected function getSubjectTranslation(): string
    {
        return match ($this->comment->type) {
            TicketThreadType::SOLVED_COMMENT => __('mail.ticket.comment.solution', ['id' => $this->ticket->id]),
            TicketThreadType::CLOSE_COMMENT => __('mail.ticket.comment.close', ['id' => $this->ticket->id]),
            TicketThreadType::APPROVE_COMMENT => __('mail.ticket.comment.approve', ['id' => $this->ticket->id]),
            TicketThreadType::DECLINE_COMMENT => __('mail.ticket.comment.decline', ['id' => $this->ticket->id]),
            TicketThreadType::REOPEN_COMMENT => __('mail.ticket.comment.reopen', ['id' => $this->ticket->id]),
            default => __('mail.ticket.comment.new', ['id' => $this->ticket->id]),
        };
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new_comment',
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
