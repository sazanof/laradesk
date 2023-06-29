<?php

namespace App\Helpdesk;

use App\Models\User;

class TicketParticipant
{
    const REQUESTER = 1;
    const ASSIGNEE = 2;
    const APPROVAL = 3;
    const OBSERVER = 4;

    public static function getAdministrators()
    {
        return User::with('notifications')->where('is_admin', true)->get();
    }
}
