<?php

namespace App\Helpers;

use App\Models\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ConfigHelper
{
    public static function getValue(string|ConfigKey $key)
    {
        $config = Config::where('key', $key instanceof ConfigKey ? $key->value : $key);
        if ($config->count() === 1) {
            return Config::where('key', $key)->first()->value;
        }
        return null;
    }

    /**
     * @param ConfigKey $key
     * @param string|null $value
     * @return Config|false|\Illuminate\Database\Eloquent\Model
     */
    public static function updateValue(ConfigKey $key, ?string $value)
    {
        if (!is_null($value)) {
            return Config::updateOrCreate(['key' => $key->value], ['value' => $value, 'description' => '']);
        }
        return false;
    }

    public static function uploadFileAndUpdateValue(ConfigKey $key, ?UploadedFile $value)
    {
        if ($value instanceof UploadedFile) {
            $name = 'config/' . $value->getClientOriginalName();
            if (Storage::disk('public')->put($name, $value->getContent())) {
                $url = Storage::url($name);
                ConfigHelper::updateValue($key, $url);
                return $url;
            }
        }
        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed|string|null
     */
    public static function getMaxFileSize()
    {
        return self::getValue(ConfigKey::MaxFileSize->value);
    }

    /**
     * @return array|string[]
     */
    public static function getAllowedMimes()
    {
        $mimes = self::getValue(ConfigKey::AllowedMimes->value);
        if (!is_null($mimes)) {
            return explode(',', $mimes);
        }
        return [];
    }
}
