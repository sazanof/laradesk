<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationSettingsController extends Controller
{
    public function getUserNotifications()
    {
        return [
            'email' => NotificationSetting::emailNotificationsEnabled(),
            'telegram' => NotificationSetting::telegramNotificationsEnabled()
        ];
    }

    public function updateUserNotifications(Request $request)
    {
        foreach ($request->only(['email', 'telegram']) as $key => $item) {
            $type = null;
            switch ($key) {
                case 'email':
                    $type = NotificationSetting::TYPE_EMAIL;
                    break;
                case 'telegram':
                    $type = NotificationSetting::TYPE_TELEGRAM;
                    break;
            }
            if ($item) {
                NotificationSetting::insertOrIgnore([
                    'user_id' => Auth::id(),
                    'type' => $type
                ]);
            } else {
                NotificationSetting
                    ::where('user_id', Auth::id())
                    ->where('type', $type)
                    ->delete();
            }
        }
        return $this->getUserNotifications();
    }
}
