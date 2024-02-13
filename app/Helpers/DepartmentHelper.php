<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
}
