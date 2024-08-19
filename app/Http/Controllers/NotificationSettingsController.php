<?php

namespace App\Http\Controllers;

use App\Enums\UserNotificationTypeEnum;
use App\Helpers\UserNotificationHelper;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Models\UserNotificationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationSettingsController extends Controller
{
    /**
     * @return array
     */
    public function getUserNotifications(): array
    {
        $details = [];

        $details_telegram = UserNotificationDetail
            ::where('type', UserNotificationTypeEnum::NEW_TICKET->value)
            ->where('user_id', Auth::id())
            ->first();

        $details_comment = UserNotificationDetail
            ::where('type', UserNotificationTypeEnum::NEW_COMMENT->value)
            ->where('user_id', Auth::id())
            ->first();

        $details_approval = UserNotificationDetail
            ::where('type', UserNotificationTypeEnum::NEW_APPROVAL->value)
            ->where('user_id', Auth::id())
            ->first();

        $details_observer = UserNotificationDetail
            ::where('type', UserNotificationTypeEnum::NEW_OBSERVER->value)
            ->where('user_id', Auth::id())
            ->first();

        $details_assignee = UserNotificationDetail
            ::where('type', UserNotificationTypeEnum::NEW_ASSIGNEE->value)
            ->where('user_id', Auth::id())
            ->first();

        $details['ticket'] = $details_telegram ? $details_telegram->active : false;
        $details['comment'] = $details_comment ? $details_comment->active : false;
        $details['approval'] = $details_approval ? $details_approval->active : false;
        $details['observer'] = $details_observer ? $details_observer->active : false;
        $details['assignee'] = $details_assignee ? $details_assignee->active : false;
        return [
            'email' => NotificationSetting::emailNotificationsEnabled(),
            'telegram' => NotificationSetting::telegramNotificationsEnabled(),
            'details' => $details
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function updateUserNotifications(Request $request): array
    {
        foreach ($request->only(['email', 'telegram', 'details']) as $key => $item) {
            $type = null;
            switch ($key) {
                case 'email':
                    $type = NotificationSetting::TYPE_EMAIL_ENABLED;
                    break;
                case 'telegram':
                    $type = NotificationSetting::TYPE_TELEGRAM_ENABLED;
                    break;
                case 'details':
                    /** @var User $user */
                    $user = $request->user();
                    DB::transaction(function () use ($request, $user) {
                        $details = $request->get('details');
                        $detail_ticket = $details['ticket'];
                        $detail_comment = $details['comment'];
                        $detail_approval = $details['approval'];
                        $detail_observer = $details['observer'];
                        $detail_assignee = $details['assignee'];

                        UserNotificationHelper::updateSetting(
                            user: $user,
                            noty: UserNotificationTypeEnum::NEW_TICKET,
                            active: $detail_ticket
                        );

                        UserNotificationHelper::updateSetting(
                            user: $user,
                            noty: UserNotificationTypeEnum::NEW_COMMENT,
                            active: $detail_comment
                        );

                        UserNotificationHelper::updateSetting(
                            user: $user,
                            noty: UserNotificationTypeEnum::NEW_APPROVAL,
                            active: $detail_approval
                        );

                        UserNotificationHelper::updateSetting(
                            user: $user,
                            noty: UserNotificationTypeEnum::NEW_OBSERVER,
                            active: $detail_observer
                        );

                        UserNotificationHelper::updateSetting(
                            user: $user,
                            noty: UserNotificationTypeEnum::NEW_ASSIGNEE,
                            active: $detail_assignee
                        );
                    });
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
