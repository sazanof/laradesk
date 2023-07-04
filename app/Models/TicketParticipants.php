<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TicketParticipants
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property int $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants withoutTrashed()
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereDeletedAt($value)
 * @property int|null $approved
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\TicketParticipantsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipants whereApproved($value)
 * @mixin \Eloquent
 */
class TicketParticipants extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'role',
        'approved'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select(User::PUBLIC_FIELDS);
    }
}
