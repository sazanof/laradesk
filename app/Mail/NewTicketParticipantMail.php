<?php

namespace App\Mail;

use App\Helpdesk\TicketParticipant;
use App\Helpers\MailRecipients;
use App\Models\Config;
use App\Models\Ticket;
use App\Models\TicketParticipants;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTicketParticipantMail extends Mailable
{
    use Queueable, SerializesModels;

    public TicketParticipants|User $participant;
    public Ticket $ticket;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct(TicketParticipants|User $participant, Ticket $ticket)
    {
        $this->participant = $participant;
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tpl = '';
        switch ($this->participant->role) {
            case TicketParticipant::ASSIGNEE:
                $tpl = 'mail.ticket.participant.assignee';
                break;
            case TicketParticipant::APPROVAL:
                $tpl = 'mail.ticket.participant.approval';
                break;
            case TicketParticipant::OBSERVER:
                $tpl = 'mail.ticket.participant.observer';
                break;
        }
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), Config::appName()),
            to: MailRecipients::single($this->participant),
            subject: __($tpl, [
                'id' => $this->ticket->id
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new_participant',
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
