<?php

namespace App\Helpers;

use App\Helpdesk\TicketParticipant;
use App\Models\NotificationSetting;
use App\Models\Ticket;
use App\Models\TicketParticipants;
use App\Models\User;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Log;

class MailRecipients
{
    /**
     * @return array
     */
    public static function administrators(): array
    {
        $recipients = [];
        foreach (TicketParticipant::getAdministrators() as $administrator) {
            if (
                filter_var($administrator->email, FILTER_VALIDATE_EMAIL)
                && NotificationSetting::emailNotificationsEnabled($administrator->id)
            ) {
                $recipients[] = new Address($administrator->email, $administrator->firstname . ' ' . $administrator->lastname);
            }
        }
        return $recipients;
    }

    public static function single(TicketParticipants $participant)
    {
        if (
            filter_var($participant->user->email, FILTER_VALIDATE_EMAIL)
            && NotificationSetting::emailNotificationsEnabled($participant->user->id)
        ) {
            return [new Address(
                $participant->user->email,
                $participant->user->firstname . ' ' . $participant->user->lastname
            )];
        } else {
            Log::error('Invalid email in user_id ' . $participant->id);
            return false;
        }
    }

    /**
     * @param Ticket $ticket
     * @return array
     */
    public static function assigneess(Ticket $ticket): array
    {
        $recipients = [];
        /** @var TicketParticipants|User $user */
        foreach ($ticket->assignees as $user) {
            if (
                filter_var($user->email, FILTER_VALIDATE_EMAIL)
                && NotificationSetting::emailNotificationsEnabled($user->user_id)
            ) {
                $recipients[] = new Address($user->email, $user->firstname . ' ' . $user->lastname);
            }
        }
        return $recipients;
    }

    /**
     * @param Ticket $ticket
     * @return array
     */
    public static function observers(Ticket $ticket): array
    {
        $recipients = [];
        /** @var TicketParticipants|User $user */
        foreach ($ticket->observers as $user) {
            if (
                filter_var($user->email, FILTER_VALIDATE_EMAIL)
                && NotificationSetting::emailNotificationsEnabled($user->user_id)
            ) {
                $recipients[] = new Address($user->email, $user->firstname . ' ' . $user->lastname);
            }
        }
        return $recipients;
    }

    /**
     * @param Ticket $ticket
     * @return array
     */
    public static function approvals(Ticket $ticket): array
    {
        $recipients = [];
        /** @var TicketParticipants|User $user */
        foreach ($ticket->approvals as $user) {
            if (
                filter_var($user->email, FILTER_VALIDATE_EMAIL)
                && NotificationSetting::emailNotificationsEnabled($user->user_id)
            ) {
                $recipients[] = new Address($user->email, $user->firstname . ' ' . $user->lastname);
            }
        }
        return $recipients;
    }

    /**
     * @param Ticket $ticket
     * @return array
     */
    public static function commentAddresses(Ticket $ticket): array
    {
        $recipients = [];
        if (
            filter_var($ticket->requester->email, FILTER_VALIDATE_EMAIL)
            && NotificationSetting::emailNotificationsEnabled($ticket->requester->id)
        )
            $recipients[] = new Address(
                $ticket->requester->email,
                $ticket->requester->firstname . ' ' . $ticket->requester->lastname
            );
        $otherParticipants = [];
        foreach ($ticket->assignees as $user) {
            $otherParticipants[$user->user_id] = $user;
        }
        foreach ($ticket->observers as $user) {
            $otherParticipants[$user->user_id] = $user;
        }
        foreach ($ticket->approvals as $user) {
            $otherParticipants[$user->user_id] = $user;
        }
        /** @var TicketParticipants $participant */
        foreach ($otherParticipants as $participant) {
            if (
                filter_var($participant->email, FILTER_VALIDATE_EMAIL)
                && NotificationSetting::emailNotificationsEnabled($participant->user_id)
            ) {
                $recipients[] = new Address($participant->email, $participant->firstname . ' ' . $participant->lastname);
            }
        }
        return $recipients;
    }
}
