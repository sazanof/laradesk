<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $user_id
 * @property int $notification_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail whereUserId($value)
 * @property int $active
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotificationDetail whereActive($value)
 * @mixin \Eloquent
 */
class UserNotificationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];
}
