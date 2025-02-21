<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Config
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Config whereValue($value)
 * @mixin \Eloquent
 */
class Config extends Model
{
    use HasFactory;

    protected $table = 'config';

    protected $fillable = [
        'key',
        'value',
        'description'
    ];

    /**
     * @return string
     */
    public static function appName()
    {
        return Config::where('key', 'app.name')->first()->value;
    }
}
