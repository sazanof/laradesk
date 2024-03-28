<?php

namespace App\Http\Controllers;

use App\Helpdesk\Participant;
use App\Helpdesk\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getUserDashboardData(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $id = $user->id;
        return [
            'all' => Ticket::whereUserId($id)->count(),
            'waiting' => Ticket::whereUserId($id)->where('status', TicketStatus::WAITING)->count(),
            'in-approval' => Ticket::whereUserId($id)->where('status', TicketStatus::IN_APPROVAL)->count(),
            'in-work' => Ticket::whereUserId($id)->where('status', TicketStatus::IN_WORK)->count(),
            'closed' => Ticket::whereUserId($id)->where('status', TicketStatus::CLOSED)->count(),
            'solved' => Ticket::whereUserId($id)->where('status', TicketStatus::SOLVED)->count(),
        ];
    }

    public function getAdminDashboardData(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $id = $user->id;

        return [
            'all' => Ticket::activeDepartment()->count(),
            'new' => Ticket::activeDepartment()->where('status', TicketStatus::NEW)->count(),
            'waiting' => Ticket::activeDepartment()->where('status', TicketStatus::WAITING)->count(),
            'in-approval' => Ticket::where('status', TicketStatus::IN_APPROVAL)->count(),
            'in-work' => Ticket::activeDepartment()->where('status', TicketStatus::IN_WORK)->count(),
            'closed' => Ticket::where('status', TicketStatus::CLOSED)->count(),
            'solved' => Ticket::where('status', TicketStatus::SOLVED)->count(),
            'my' => Ticket::withParticipants()->onlyByRoleAndUserId(Participant::ASSIGNEE, $id)->count(),
            'i-am-approval' => Ticket::withParticipants()->onlyByRoleAndUserId(Participant::APPROVAL, $id)->count(),
        ];
    }
}
