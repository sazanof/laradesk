<?php

namespace App\Http\Controllers;

use App\Helpdesk\TicketStatus;
use App\Helpdesk\TicketThreadType;
use App\Helpers\AclHelper;
use App\Models\Ticket;
use App\Models\TicketParticipants;
use App\Models\TicketThread;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TicketThreadController extends Controller
{
    public function addSolutionComment(int $id, Request $request)
    {
        return $this->addCommentToDb($request, TicketThreadType::SOLVED_COMMENT);
    }

    public function addCloseComment(int $id, Request $request)
    {
        return $this->addCommentToDb($request, TicketThreadType::CLOSE_COMMENT);
    }

    public function addReopenComment(int $id, Request $request)
    {
        return $this->addCommentToDb($request, TicketThreadType::REOPEN_COMMENT);
    }

    /**
     * @param int $id
     * @return TicketThread[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function getTicketThread(int $id): array|Collection|\Illuminate\Support\Collection
    {
        return TicketThread::where('ticket_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addApproveComment(int $id, Request $request)
    {
        $ticketId = $request->get('ticket_id');
        $ticket = Ticket::findOrFail($ticketId);
        if (!AclHelper::isApproval($ticket)) {
            throw new AccessDeniedHttpException();
        }
        return $this->addCommentToDb($request, TicketThreadType::APPROVE_COMMENT);
    }

    public function addDeclineComment(int $id, Request $request)
    {
        $ticketId = $request->get('ticket_id');
        $ticket = Ticket::findOrFail($ticketId);
        if (!AclHelper::isApproval($ticket)) {
            throw new AccessDeniedHttpException();
        }
        return $this->addCommentToDb($request, TicketThreadType::DECLINE_COMMENT);
    }

    public function addComment(int $id, Request $request)
    {
        $ticketId = $request->get('ticket_id');
        $ticket = Ticket::findOrFail($ticketId);
        if (AclHelper::isRequester($ticket) || AclHelper::isAdmin()) {
            return $this->addCommentToDb($request, TicketThreadType::COMMENT);
        }
    }

    public function addCommentToDb(Request $request, $type = TicketThreadType::COMMENT)
    {
        $request->validate(
            [
                'content' => 'required|min:3',
                'ticket_id' => 'exists:tickets,id',
                'user_id' => 'exists:users,id',
            ]
        );
        return DB::transaction(function () use ($request, $type) {
            $comment = TicketThread::create([
                'ticket_id' => $request->get('ticket_id'),
                'user_id' => Auth::id(),
                'type' => $type,
                'content' => $request->get('content')
            ]);
            if ($type === TicketThreadType::DECLINE_COMMENT || $type === TicketThreadType::APPROVE_COMMENT) {
                TicketParticipants
                    ::where('ticket_id', $comment->ticket_id)
                    ->where('user_id', $comment->user_id)
                    ->update(['approved' => (int)$type === TicketThreadType::APPROVE_COMMENT]);
                $unApproved = TicketParticipants
                    ::where('ticket_id', $comment->ticket_id)->where(function (Builder $builder) {
                        $builder->orWhereNull('approved')->orWhere('approved', 0);
                    })->count();
                if ($unApproved === 0) {
                    Ticket::findOrFail($comment->ticket_id)->update(['status' => TicketStatus::APPROVED]);
                }
            }
            if ($type === TicketThreadType::CLOSE_COMMENT) {
                Ticket::findOrFail($comment->ticket_id)->update(['status' => TicketStatus::CLOSED]);
            }
            if ($type === TicketThreadType::REOPEN_COMMENT) {
                Ticket::findOrFail($comment->ticket_id)->update(['status' => TicketStatus::IN_WORK]);
            }
            if ($type === TicketThreadType::SOLVED_COMMENT) {
                Ticket::findOrFail($comment->ticket_id)->update(['status' => TicketStatus::SOLVED]);
            }
        });

    }

    public function editComment(int $id, Request $request)
    {

    }

    public function deleteComment(int $id)
    {

    }
}
