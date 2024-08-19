<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\NotificationSetting
 *
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting query()
 * @property int $user_id
 * @property int $type
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereUserId($value)
 * @mixin \Eloquent
 */
class NotificationSetting extends Model
{
    use HasFactory;

    const TYPE_EMAIL_ENABLED = 1;
    const TYPE_TELEGRAM_ENABLED = 2;

    protected $fillable = [
        'user_id',
        'type'
    ];

    /**
     * @param int|null $user_id
     * @return bool
     */
    public static function emailNotificationsEnabled(int $user_id = null): bool
    {
        return self::notificationEnabled(self::TYPE_EMAIL_ENABLED, $user_id);
    }

    /**
     * @param int|null $user_id
     * @return bool
     */
    public static function telegramNotificationsEnabled(int $user_id = null): bool
    {
        return self::notificationEnabled(self::TYPE_TELEGRAM_ENABLED, $user_id);
    }

    /**
     * @param int $type
     * @param int|null $user_id
     * @return bool
     */
    public static function notificationEnabled(int $type, int $user_id = null): bool
    {
        return NotificationSetting
                ::where('type', $type)
                ->where('user_id', '=', is_null($user_id) ? Auth::id() : $user_id)
                ->count() === 1;
    }
}
