<?php

namespace App\Http\Controllers;

use App\Helpdesk\TicketFromRequest;
use App\Helpdesk\TicketsFilterFromRequest;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TicketsController extends Controller
{

    protected TicketsFilterFromRequest $filterFromRequest;

    /**
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     * @throws \Throwable
     */
    public function createTicket(Request $request)
    {
        $ticket = new TicketFromRequest($request);
        $ticket->validate($request);
        return $ticket->create();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getSentTickets(Request $request)
    {
        return $this->ticketsQuery($request)->where('user_id', Auth::id())
            ->paginate(
                $this->filterFromRequest->getPerPage(),
                ['*'],
                'page',
                $this->filterFromRequest->getPage()
            );
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getTickets(Request $request): LengthAwarePaginator
    {
        return $this->ticketsQuery($request)
            ->paginate(
                $this->filterFromRequest->getPerPage(),
                ['*'],
                'page',
                $this->filterFromRequest->getPage()
            );
    }

    /**
     * @param Request $request
     * @return Builder
     */
    private function ticketsQuery(Request $request): Builder
    {
        $this->filterFromRequest = new TicketsFilterFromRequest($request);
        $query = Ticket::with([
            'fields',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals'
        ])->orderBy(
            $this->filterFromRequest->getSortField(),
            $this->filterFromRequest->getSortDirection()
        );
        if (!is_null($this->filterFromRequest->getTicketStatus())) {
            $query = $query->where('status', '=', $this->filterFromRequest->getTicketStatus());
        }
        return $query;
    }
}
