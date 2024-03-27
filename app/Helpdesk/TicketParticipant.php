<?php

namespace App\Helpdesk;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TicketParticipant
{
    const REQUESTER = 1;
    const ASSIGNEE = 2;
    const APPROVAL = 3;
    const OBSERVER = 4;

    /**
     * @param int|null $departmentId
     * @return User[]|Collection
     */
    public static function getAdministrators(int $departmentId = null)
    {
        $users = User::with('system_notifications')->where('is_admin', true);
        if (!is_null($departmentId)) {
            $users = $users
                ->select([
                    'users.*',
                    'ad.id as admin_departments_id',
                    'ad.admin_id as admin_id',
                ])
                ->join('admin_departments as ad', 'ad.admin_id', 'users.id');
            $users = $users->where(function (Builder $builder) use ($departmentId) {
                return $builder->where('department_id', $departmentId);
            });
        }
        return $users->get();
    }
}
