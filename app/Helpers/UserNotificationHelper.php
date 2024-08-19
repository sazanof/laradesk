<?php

namespace App\Helpers;

use App\Enums\UserNotificationTypeEnum;
use App\Models\User;
use App\Models\UserNotificationDetail;

/**
 * Helper class for user_notification_details
 */
class UserNotificationHelper
{
    public static function updateSetting(User $user, UserNotificationTypeEnum $noty, bool $active): void
    {
        $existing = UserNotificationDetail
            ::where('user_id', $user->id)
            ->where('type', $noty->value)
            ->first();

        if (!$existing) {
            UserNotificationDetail::create([
                'user_id' => $user->id,
                'type' => $noty->value,
                'enabled' => $active
            ]);
        } else {
            $existing->active = $active;
            $existing->save();
        }
    }

    /**
     * @param User $user
     * @param UserNotificationTypeEnum $noty
     * @return bool
     */
    public static function isActive(User $user, UserNotificationTypeEnum $noty): bool
    {
        $existing = UserNotificationDetail
            ::where('user_id', $user->id)
            ->where('type', $noty->value)
            ->first();
        return $existing && $existing->active;
    }
}
