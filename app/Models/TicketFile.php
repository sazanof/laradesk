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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereUserId($value)
 * @property int $size
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereSize($value)
 * @property string $extension
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFile whereExtension($value)
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
