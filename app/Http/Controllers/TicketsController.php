<?php

namespace App\Http\Controllers;

use App\Events\NewParticipant;
use App\Events\NewTicket;
use App\Helpdesk\TicketFromRequest;
use App\Helpdesk\TicketParticipant;
use App\Helpdesk\TicketStatus;
use App\Helpers\AclHelper;
use App\Helpers\DepartmentHelper;
use App\Helpers\RequestBuilder;
use App\Models\Ticket;
use App\Models\TicketFields;
use App\Models\TicketParticipants;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketsController extends Controller
{

    protected RequestBuilder $requestBuilder;

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
        NewTicket::dispatch($t);
        return $t->only('id');
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function getCounters(Request $request): array //departmentId
    {

        /** @var User $user */
        $user = $request->user();
        if ($user === null) return [];
        $id = DepartmentHelper::getDepartment($user);
        $new = Ticket
            ::select()
            ->whereIn('status', [TicketStatus::NEW, TicketStatus::IN_APPROVAL]);
        if (!is_null($id)) {
            $new = $new->where('department_id', $id);
        }
        $new = $new->count('tickets.id');

        $my = Ticket::activeDepartment()
            ->withParticipants()
            ->onlyByRoleAndUserId(TicketParticipant::ASSIGNEE, $user->id)
            ->count();

        $approval = Ticket::select(['tickets.*'])
            ->whereNotIn('tickets.status', [TicketStatus::SOLVED, TicketStatus::CLOSED, TicketStatus::APPROVED])
            ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
            ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id')
            ->where('tp.role', TicketParticipant::APPROVAL)
            ->where('tp.user_id', Auth::id());
        /*if (!is_null($id)) {
            $approval = $approval->where('tickets.department_id', $id);
        }*/
        $approval = $approval->count('tickets.id');
        if (AclHelper::isAdmin()) {
            return compact('approval', 'my', 'new');
        }
        return compact('approval');
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getUserTickets(Request $request): LengthAwarePaginator
    {
        $q = $this->ticketsQuery($request);
        return $q
            ->paginate(
                $this->requestBuilder->getPerPage(),
                ['*'],
                'page',
                $this->requestBuilder->getPage()
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
            'department',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals'
        ]);
    }

    /**
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function deleteTicket(int $id): void
    {
        $ticket = Ticket::withTrashed()->find($id);
        if (!$ticket->trashed()) {
            TicketParticipants::withTrashed()->where('ticket_id', $ticket->id)->delete();
            TicketThread::withTrashed()->where('ticket_id', $ticket->id)->delete();
            TicketFields::withTrashed()->where('ticket_id', $ticket->id)->delete();
            $ticket->delete();
        } else {
            throw new \Exception('Force delete nt implemented');
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function restoreTicket(int $id): void
    {
        $ticket = Ticket::withTrashed()->find($id);
        if ($ticket->trashed()) {
            $ticket->restore();
            TicketParticipants::withTrashed()->where('ticket_id', $ticket->id)->restore();
            TicketThread::withTrashed()->where('ticket_id', $ticket->id)->restore();
            TicketFields::withTrashed()->where('ticket_id', $ticket->id)->restore();
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Ticket|array|null
     */
    public function getUserTicket(int $id): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|Ticket|array|null
    {
        $ticket = Ticket::find($id)->load([
            'fields',
            'department',
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
        throw new NotFoundHttpException('Ticket not found, or you haven\'t access to it');
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function getTickets(Request $request): LengthAwarePaginator
    {
        return $this->ticketsQuery($request)
            ->paginate(
                $this->requestBuilder->getPerPage(),
                ['*'],
                'page',
                $this->requestBuilder->getPage()
            );
    }

    /**
     * @param Request $request
     * @return Builder
     */
    private function ticketsQuery(Request $request): Builder
    {
        $query = Ticket::with([
            'fields',
            'department',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals'
        ]);
        $this->requestBuilder = new RequestBuilder($request, $query);
        return $this->requestBuilder->getBuilder();
    }

    public function addParticipant(int $id, Request $request)
    {
        $user_ids = $request->get('user_id');
        if (is_numeric($user_ids)) {
            $user_ids = [$user_ids];
        }
        $type = $request->get('type');
        $ticket = Ticket::find($id);
        if (($ticket->user_id !== Auth::id()) && !$request->user()->is_admin) {
            throw new \Exception('You can not add participants.');
        }
        try {
            if (empty($user_ids)) {
                return false;
            }
            foreach ($user_ids as $uid) {
                TicketParticipants::updateOrCreate([
                    'user_id' => $uid,
                    'ticket_id' => $ticket->id,
                    'role' => $type
                ], [
                    'user_id' => $uid,
                    'ticket_id' => $ticket->id,
                    'role' => $type,
                    'deleted_at' => null
                ]);
            }


        } catch (QueryException $exception) {
            TicketParticipants::withTrashed()->whereIn('user_id', $user_ids)
                ->where('ticket_id', $ticket->id)
                ->where('role', $type)
                ->restore();
        }
        $ticket->status = TicketStatus::IN_WORK;
        $ticket->save();
        $ticket->refresh();
        $p = null;
        // Get only newly participants
        switch ($type) {
            case TicketParticipant::ASSIGNEE:
                $p = $ticket->assignees()->whereIn('user_id', $user_ids)->get();
                break;
            case TicketParticipant::APPROVAL:
                $p = $ticket->approvals()->whereIn('user_id', $user_ids)->get();
                break;
            case TicketParticipant::OBSERVER:
                $p = $ticket->observers()->whereIn('user_id', $user_ids)->get();
                break;
        }
        if (!is_null($p)) {
            NewParticipant::dispatch($p, $ticket);
        }

        return $p;
    }

    public function removeParticipant(int $id, Request $request)
    {
        $ticket = Ticket::find($id);
        if (($ticket->user_id !== Auth::id()) && !$request->user()->is_admin) {
            throw new \Exception('You can not add participants.');
        }
        $participantUserId = $request->get('id');
        $type = $request->get('type');
        TicketParticipants::find($participantUserId)->delete();
        switch ($type) {
            case TicketParticipant::OBSERVER:
                return $ticket->observers;
            case TicketParticipant::APPROVAL:
                return $ticket->approvals;
            case TicketParticipant::ASSIGNEE:
                return $ticket->assignees;
        }
    }
}
