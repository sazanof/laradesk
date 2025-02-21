<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TicketThread
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property int $type
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketThreadCommentFile> $files
 * @property-read int|null $files_count
 * @property-read \App\Models\Ticket|null $ticket
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\TicketThreadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThread withoutTrashed()
 * @mixin \Eloquent
 */
class TicketThread extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'type',
        'content'
    ];


    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone(env('APP_TIMEZONE'))->format('d.m.Y H:i');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function files()
    {
        return $this->hasMany(TicketThreadCommentFile::class, 'thread_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
}
