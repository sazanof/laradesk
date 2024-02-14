<?php

namespace App\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Helpers\ConfigKey;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function SaveSettings(Request $request)
    {
        try {
            $helper = SettingsHelper::createFromRequest($request);
            ConfigHelper::updateValue(ConfigKey::Name, $helper->name);
            ConfigHelper::updateValue(ConfigKey::MaxFileSize, $helper->maxFileSize);

            if (is_array($helper->allowedMimes)) {
                ConfigHelper::updateValue(ConfigKey::AllowedMimes, implode(',', $helper->allowedMimes));
            }

            ConfigHelper::uploadFileAndUpdateValue(ConfigKey::Logo, $helper->logo);
            ConfigHelper::uploadFileAndUpdateValue(ConfigKey::Bg, $helper->bg);

            return [
                'success' => true
            ];
        } catch (\Exception $e) {
            Log::error('[SaveSettings] ' . $e->getMessage());
        }

    }
}
