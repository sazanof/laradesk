<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Connection
 *
 * @property int $user_id
 * @property int $conn_id
 * @method static \Illuminate\Database\Eloquent\Builder|Connection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Connection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Connection query()
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereConnId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Connection whereUserId($value)
 * @mixin \Eloquent
 */
class Connection extends Model
{
    use HasFactory;

    protected $table = 'connections';
    protected $fillable = [
        'user_id',
        'conn_id'
    ];
}
