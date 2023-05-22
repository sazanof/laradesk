<?php

namespace App\Helpers;

use App\Models\Config;

class ConfigHelper
{
    public static function getValue(string $key)
    {
        return Config::where('key', $key)->first()->value;
    }
}
