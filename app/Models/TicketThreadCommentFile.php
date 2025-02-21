<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TicketThreadComment
 *
 * @property int $id
 * @property int $user_id
 * @property int $ticket_id
 * @property int $thread_id
 * @property string $file
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $author
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketThreadCommentFile whereUserId($value)
 * @mixin \Eloquent
 */
class TicketThreadCommentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'thread_id',
        'file',
        'name'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
