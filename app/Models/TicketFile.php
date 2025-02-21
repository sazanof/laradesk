<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $name
 * @property string $path
 * @property int $size
 * @property string $extension
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketFile whereUserId($value)
 * @mixin \Eloquent
 */
class TicketFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'name',
        'path',
        'size',
        'extension'
    ];
}
