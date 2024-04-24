<?php

namespace App\Enums;

enum SubCriteria: string
{
    case NEW = 'new';
    case  IN_WORK = 'in-work';
    case WAITING = 'waiting';
    case SOLVED = 'solved';
    case CLOSED = 'closed';
    case IN_APPROVAL = 'in-approval';
    case  APPROVED = 'approved';
    case I_AM_APPROVAL = 'i-am-approval';
    case IN_OBSERVING = 'in-observing';
    case MY = 'my';
}
