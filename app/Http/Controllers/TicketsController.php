<?php

namespace App\Http\Controllers;

use App\Events\NewTicket;
use App\Helpdesk\TicketFromRequest;
use App\Helpdesk\TicketParticipant;
use App\Helpdesk\TicketsFilterFromRequest;
use App\Helpdesk\TicketStatus;
use App\Helpers\AclHelper;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $t = $ticket->create();
        NewTicket::dispatch(Ticket::findOrFail($t['id']));
        return $t;
    }

    /**
     * @return array
     */
    public function getCounters()
    {
        $new = Ticket::select()->whereIn('status', [TicketStatus::NEW, TicketStatus::IN_APPROVAL])->count('tickets.id');
        $my = Ticket::select()
            ->select(['tickets.*'])
            ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
            ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id')
            ->where('tp.role', TicketParticipant::ASSIGNEE)
            ->where('tp.user_id', Auth::id())->count('tickets.id');
        $approval = Ticket::select()
            ->select(['tickets.*'])
            ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
            ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id')
            ->where('tp.role', TicketParticipant::APPROVAL)
            ->where('tp.user_id', Auth::id())->count('tickets.id');
        if (AclHelper::isAdmin()) {
            return compact('approval', 'my', 'new');
        }
        return compact('approval');
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getUserTickets(Request $request)
    {
        $q = $this->ticketsQuery($request);
        return $q
            ->paginate(
                $this->filterFromRequest->getPerPage(),
                ['*'],
                'page',
                $this->filterFromRequest->getPage()
            );
    }

    /**
     * @param int $id
     * @return Ticket|Ticket[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getTicket(int $id): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Ticket|array|null
    {
        return Ticket::find($id)->load([
            'fields',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals'
        ]);
    }

    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Ticket|array|null
     */
    public function getUserTicket(int $id): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Ticket|array|null
    {
        $ticket = Ticket::find($id)->load([
            'fields',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals'
        ]);
        if ($ticket->approvals()->where('user_id', Auth::id())->count() === 1) {
            return $ticket;
        }
        if ($ticket->user_id === Auth::id()) {
            return $ticket;
        }
        throw new NotFoundHttpException();
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
        $query = $this->filterFromRequest->criteriaToQueryPart($query);
        if ($this->filterFromRequest->getCriteria() === 'my') {
            $query
                ->select(['tickets.*'])
                ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
                ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id')
                ->where('tp.role', TicketParticipant::ASSIGNEE)
                ->where('tp.user_id', Auth::id());
        }
        if ($this->filterFromRequest->getCriteria() === 'approval') {
            $query
                ->select(['tickets.*'])
                ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
                ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id')
                ->where('tp.role', TicketParticipant::APPROVAL)
                ->where('tp.user_id', Auth::id());
        }
        return $query;
    }
}
