<?php

namespace App\Helpdesk;

enum TicketThreadType: int
{
    case COMMENT = 1;
    case SOLVED_COMMENT = 2;
    case CLOSE_COMMENT = 3;
    case APPROVE_COMMENT = 4;
    case DECLINE_COMMENT = 5;
    case REOPEN_COMMENT = 6;
}
