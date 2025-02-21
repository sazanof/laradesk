<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property string|null $text
 * @property int $published
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $only_admins
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereOnlyAdmins($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withoutTrashed()
 * @mixin \Eloquent
 */
class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'text',
        'only_admins'
    ];
}
