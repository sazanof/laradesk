<?php

namespace App\Http\Controllers;

use App\Helpdesk\TicketStatus;
use App\Models\AdminDepartments;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    public function getStatuses()
    {
        /**
         * const NEW = 1;
         * const IN_WORK = 2;
         * const WAITING = 3;
         * const SOLVED = 4;
         * const CLOSED = 5;
         * const IN_APPROVAL = 6;
         * const APPROVED = 7;
         */

        return [
            TicketStatus::NEW => __('New'),
            TicketStatus::IN_WORK => __('In work'),
            TicketStatus::WAITING => __('Waiting'),
            TicketStatus::IN_APPROVAL => __('In approval'),
            TicketStatus::APPROVED => __('Approved'),
            TicketStatus::SOLVED => __('Solved'),
            TicketStatus::CLOSED => __('Closed'),
        ];
    }

    public function getTickets(Request $request)
    {
        $departmentId = $request->get('department_id');
        /** @var User $user */
        $user = $request->user();
        $user->departments->filter(function (AdminDepartments $department) use ($departmentId) {
            return $department->department_id = $departmentId;
        });
        $items = collect();
        foreach ($this->getStatuses() as $status => $label) {
            $tickets = Ticket
                ::query()
                ->with('requester', 'assignees')
                ->where('department_id', $departmentId)
                ->where('status', $status)
                ->orderByDesc('created_at')
                ->paginate(100);
            $items->add(
                [
                    'status' => compact('label', 'status'),
                    'tickets' => $tickets
                ]
            );
        }
        return $items;
    }
}
