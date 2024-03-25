<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Department
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $email
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Department withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Department withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $ticketsCount
 * @property-read int|null $tickets_count_count
 * @mixin \Eloquent
 */
class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    public function categories()
    {
        return $this
            ->hasMany(Category::class, 'department_id', 'id')
            ->with('parentCategory')
            ->select(['id', 'name', 'parent', 'department_id']);
    }

    public function members()
    {
        return $this->hasManyThrough(
            User::class,
            AdminDepartments::class,
            'department_id',
            'id',
            'id',
            'admin_id'
        );
    }

    public function ticketsCount()
    {
        return $this
            ->hasMany(Ticket::class, 'department_id', 'id')
            ->selectRaw('COUNT(id) as total')
            ->groupBy('department_id')->whereNull('deleted_at');
    }
}
