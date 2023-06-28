<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TicketThread
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property int $type
 * @property string $content
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread whereUserId($value)
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\TicketThreadFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class TicketThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'type',
        'content'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
