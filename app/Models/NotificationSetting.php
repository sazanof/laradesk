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
 * @mixin \Eloquent
 */
class NotificationSetting extends Model
{
    use HasFactory;

    const TYPE_EMAIL = 1;
    const TYPE_TELEGRAM = 2;

    protected $fillable = [
        'user_id',
        'type'
    ];

    /**
     * @return bool
     */
    public static function emailNotificationsEnabled(): bool
    {
        return self::notificationEnabled(self::TYPE_EMAIL);
    }

    /**
     * @return bool
     */
    public static function telegramNotificationsEnabled(): bool
    {
        return self::notificationEnabled(self::TYPE_TELEGRAM);
    }

    /**
     * @param int $type
     * @return bool
     */
    public static function notificationEnabled(int $type): bool
    {
        return NotificationSetting
                ::where('type', $type)
                ->where('user_id', Auth::id())
                ->count() === 1;
    }
}
