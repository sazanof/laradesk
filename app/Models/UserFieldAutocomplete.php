<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $field_id
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFieldAutocomplete whereValue($value)
 * @mixin \Eloquent
 */
class UserFieldAutocomplete extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_id',
        'value'
    ];
}
