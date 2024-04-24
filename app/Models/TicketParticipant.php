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
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant withoutTrashed()
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereDeletedAt($value)
 * @property int|null $approved
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\TicketParticipantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|TicketParticipant whereApproved($value)
 * @property-read \App\Models\Ticket|null $ticket
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
