<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Office
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read int|null $rooms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office withoutTrashed()
 * @mixin \Eloquent
 */
class Office extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address'
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'office_id', 'id')->orderBy('name');
    }
}
