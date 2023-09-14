<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminDepartments
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments query()
 * @property int $id
 * @property int $admin_id
 * @property int $department_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Department|null $department
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminDepartments whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminDepartments extends Model
{
    protected $table = 'admin_departments';
    use HasFactory;

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
}
