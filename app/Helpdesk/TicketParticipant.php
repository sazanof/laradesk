<?php

namespace App\Helpdesk;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TicketParticipant
{
    const REQUESTER = 1;
    const ASSIGNEE = 2;
    const APPROVAL = 3;
    const OBSERVER = 4;

    public static function getAdministrators(int $departmentId = null)
    {
        $users = User::with('notifications')->where('is_admin', true);
        if (!is_null($departmentId)) {
            $users = $users->join('admin_departments as ad', 'ad.admin_id', 'users.id');
            $users = $users->where(function (Builder $builder) use ($departmentId) {
                return $builder->where('department_id', $departmentId);
            });
        }
        return $users->get();
    }
}
