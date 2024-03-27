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
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketThread withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketThreadCommentFile> $files
 * @property-read int|null $files_count
 * @property-read \App\Models\Ticket|null $ticket
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

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d.m.Y H:i',
            'updated_at' => 'datetime:d.m.Y H:i',
        ];
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
