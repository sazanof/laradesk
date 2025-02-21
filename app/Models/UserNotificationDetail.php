<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationDetail whereUserId($value)
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
