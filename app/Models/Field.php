<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Field
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string|null $options
 * @property int $is_default
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field withoutTrashed()
 * @mixin \Eloquent
 */
class Field extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'options',
        'is_default'
    ];
}
