<?php

namespace App\Helpdesk;

class TicketStatus
{
    const NEW = 1;
    const IN_WORK = 2;
    const WAITING = 3;
    const SOLVED = 4;
    const CLOSED = 5;
    const IN_APPROVAL = 6;
    const APPROVED = 7;

    const MY = 100;

    const OPEN = [
        self::NEW,
        self::IN_WORK,
        self::WAITING,
        self::IN_APPROVAL
    ];

    const NOT_OPEN = [
        self::CLOSED,
        self::SOLVED
    ];

}
