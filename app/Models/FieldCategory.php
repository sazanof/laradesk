<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * \App\Models\FieldCategory
 *
 * @property int $category_id
 * @property int $field_id
 * @property int $order
 * @property int $required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory whereUpdatedAt($value)
 * @property int $id
 * @property-read \App\Models\Field|null $field
 * @method static \Illuminate\Database\Eloquent\Builder|FieldCategory whereId($value)
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
