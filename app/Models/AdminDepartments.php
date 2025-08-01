<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminDepartments
 *
 * @property int $id
 * @property int $admin_id
 * @property int $department_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $is_default
 * @property-read \App\Models\Department|null $department
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminDepartments whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminDepartments extends Model
{
    protected $table = 'admin_departments';
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'department_id',
        'is_default'
    ];

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id')->with('categories');
    }
}
