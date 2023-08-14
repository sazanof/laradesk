<?php

namespace App\Http\Controllers;

use App\Helpdesk\TicketParticipant;
use App\Helpdesk\TicketStatus;
use App\Models\Ticket;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getUserDashboardData()
    {
        $id = Auth::id();
        return [
            'all' => Ticket::whereUserId($id)->count(),
            'waiting' => Ticket::whereUserId($id)->where('status', TicketStatus::WAITING)->count(),
            'in-approval' => Ticket::whereUserId($id)->where('status', TicketStatus::IN_APPROVAL)->count(),
            'in-work' => Ticket::whereUserId($id)->where('status', TicketStatus::IN_WORK)->count(),
            'closed' => Ticket::whereUserId($id)->where('status', TicketStatus::CLOSED)->count(),
            'solved' => Ticket::whereUserId($id)->where('status', TicketStatus::SOLVED)->count(),
        ];
    }

    public function getAdminDashboardData()
    {
        $id = Auth::id();

        return [
            'all' => Ticket::count(),
            'new' => Ticket::where('status', TicketStatus::NEW)->count(),
            'waiting' => Ticket::where('status', TicketStatus::WAITING)->count(),
            'in-approval' => Ticket::where('status', TicketStatus::IN_APPROVAL)->count(),
            'in-work' => Ticket::where('status', TicketStatus::IN_WORK)->count(),
            'closed' => Ticket::where('status', TicketStatus::CLOSED)->count(),
            'solved' => Ticket::where('status', TicketStatus::SOLVED)->count(),
            'my' => Ticket::withParticipants()->onlyByRoleAndUserId(TicketParticipant::ASSIGNEE, $id)->count(),
            'i-am-approval' => Ticket::withParticipants()->onlyByRoleAndUserId(TicketParticipant::APPROVAL, $id)->count(),
        ];
    }
}
