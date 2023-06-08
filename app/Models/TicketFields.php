<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TicketFields
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $category_id
 * @property int $field_id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketFields withoutTrashed()
 * @mixin \Eloquent
 */
class TicketFields extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'category_id',
        'field_id',
        'content'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function field(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Field::class)->select(['name', 'description']);
    }
}
