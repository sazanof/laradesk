<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * \App\Models\FieldCategory
 *
 * @property int $id
 * @property int $category_id
 * @property int $field_id
 * @property int $order
 * @property int $required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Field|null $field
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FieldCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FieldCategory extends Model
{
    use HasFactory;

    protected $table = 'category_fields';

    protected $fillable = [
        'field_id',
        'category_id',
        'order',
        'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function field(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Field::class, 'id', 'field_id');
    }
}
