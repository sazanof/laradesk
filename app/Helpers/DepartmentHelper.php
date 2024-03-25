<?php

namespace App\Helpers;

use App\Models\AdminDepartments;
use App\Models\Department;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DepartmentHelper
{
    protected const SESSION_KEY = 'active_department';

    public static function checkDepartment(User $user): bool
    {
        $deps = $user->departments;
        if ($deps->count() > 0) {
            $default = $deps->where('is_default', true);
            if ($default->count() > 0) {
                return $default->first()->id;
            } else {
                self::setDepartment($user, $deps->first()->id);
            }
        } else {
            Session::remove(self::SESSION_KEY);
            return false;
        }
        return false;
    }

    /**
     * @param User $user
     * @return int|null
     */
    public static function getDepartment(User $user): ?int
    {
        $default = $user->departments->where('is_default', true);
        return $default->count() === 1 ? $default->first()->department_id : null;
    }


    /**
     * @param User $user
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public static function setDepartment(User $user, int $id)
    {
        $user->departments()->where('is_default', true)->update(['is_default' => false]);
        $d = $user->departments()->where('department_id', $id);
        $d->update(['is_default' => true]);
        return $d->first();
    }

    /**
     * @param User|null $user
     * @param bool $loadRelations
     * @param bool $onlyIds
     * @return Department[]|\Illuminate\Database\Eloquent\Builder[]|Collection|Builder[]|\Illuminate\Support\Collection|string[]|void|null
     * @throws Exception
     */
    public static function getUserDepartments(?User $user, bool $loadRelations = false, bool $onlyIds = false)
    {
        try {
            if ($user instanceof User) {
                $adminDepartments = AdminDepartments
                    ::where('admin_id', $user->id)
                    ->selectRaw('GROUP_CONCAT(department_id) as ids')
                    ->firstOrFail();
                if (!is_null($adminDepartments)) {

                    if ($onlyIds) {
                        return explode(',', $adminDepartments->ids);
                    }
                    $departments = Department::query();
                    if ($loadRelations) {
                        $departments->with('ticketsCount');
                    }
                    return $departments->whereIn('id', explode(',', $adminDepartments->ids))->get();
                }
                return null;
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }
}
