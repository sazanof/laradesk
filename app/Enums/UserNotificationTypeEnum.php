<?php

namespace App\Enums;

enum UserNotificationTypeEnum: int
{
    case NEW_TICKET = 1;

    case NEW_COMMENT = 2;

    case NEW_APPROVAL = 3;

    case NEW_OBSERVER = 4;

    case NEW_ASSIGNEE = 5;
}
