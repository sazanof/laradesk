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

    public static function setDepartment(User $user, int $id): void
    {
        $user->departments()->where('is_default', true)->update(['is_default' => false]);
        $user->departments()->where('department_id', $id)->update(['is_default' => true]);
    }
}
