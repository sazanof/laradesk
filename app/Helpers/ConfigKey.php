<?php

namespace App\Helpers;

enum ConfigKey: string
{
    case Bg = 'app.bg';
    case Logo = 'app.logo';
    case Favicon = 'app.favicon';
    case Name = 'app.name';
    case MaxFileSize = 'app.max-file-size';
    case AllowedMimes = 'app.allowed-mimes';
}
