<?php

namespace App\Http\Controllers;

use App\Helpdesk\Participant;
use App\Helpdesk\TicketStatus;
use App\Helpers\TicketsCounterHelper;
use App\Models\Ticket;
use App\Models\User;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getUserDashboardData(Request $request)
    {
        return [
            'all' => TicketsCounterHelper::sentAll(),
            'in-observing' => TicketsCounterHelper::userIsObserver(),
            'in-approval' => TicketsCounterHelper::sentOnApprooval(),
            'waiting' => TicketsCounterHelper::sentWaiting(),
            'in-work' => TicketsCounterHelper::sentInWork(),
            'solved' => TicketsCounterHelper::sentSolved(),
            'closed' => TicketsCounterHelper::sentClosed(),
        ];
    }

    public function getAdminDashboardData(Request $request)
    {
        return [
            'all' => TicketsCounterHelper::adminAll(),
            'new' => TicketsCounterHelper::adminNew(),
            'my' => TicketsCounterHelper::adminIsAssignee(),
            'i-am-approval' => TicketsCounterHelper::userIsApproval(),
            'in-approval' => TicketsCounterHelper::adminOnApproval(),
            'waiting' => TicketsCounterHelper::adminWaiting(),
            'in-work' => TicketsCounterHelper::adminInWork(),
            'solved' => TicketsCounterHelper::adminSolved(),
            'closed' => TicketsCounterHelper::adminClosed(),
        ];
    }
}
