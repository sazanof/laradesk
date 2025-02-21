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
 * @property int|null $approved
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Ticket|null $ticket
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\TicketParticipantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketParticipant withoutTrashed()
 * @mixin \Eloquent
 */
class TicketParticipant extends Model
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

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
}
