<?php

namespace App\Helpers;

use App\Helpdesk\TicketsStorage;
use Illuminate\Support\Facades\Storage;

class FieldHelper
{
    public const TYPE_TEXT = 'TEXT';

    public const TYPE_TEXTAREA = 'TEXTAREA';

    public const TYPE_RICHTEXT = 'RICHTEXT';

    public const TYPE_DROPDOWN = 'DROPDOWN';

    public const TYPE_RADIO = 'RADIO';

    public const TYPE_CHECKBOX = 'CHECKBOX';

    public const TYPE_FILE = 'FILE';
}
