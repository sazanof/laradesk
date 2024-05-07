<?php

namespace App\Http\Controllers;

use App\Events\NewComment;
use App\Helpdesk\Participant;
use App\Helpdesk\TicketStatus;
use App\Helpdesk\TicketThreadType;
use App\Helpers\AclHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\FileUploadHelper;
use App\Models\Ticket;
use App\Models\TicketParticipant;
use App\Models\TicketThread;
use App\Models\TicketThreadCommentFile;
use App\Notifications\NewCommentNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
            ->with(['user', 'files'])
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
        if (AclHelper::isRequester($ticket) || AclHelper::isAdmin() || AclHelper::userCanCommentTicket($ticket)) {
            if (AclHelper::isAdmin()) {
                $ticket->status = TicketStatus::IN_WORK;
                $ticket->save();
            }
            return $this->addCommentToDb($request);
        }
    }

    public function addCommentToDb(Request $request, $type = TicketThreadType::COMMENT)
    {
        $files = $request->file('files');
        $maxFileSize = ConfigHelper::getMaxFileSize();
        $allowedMimes = ConfigHelper::getAllowedMimes();

        $request->validate(
            [
                'content' => 'required|min:3',
                'ticket_id' => 'exists:tickets,id',
                'user_id' => 'exists:users,id',
                'files' => 'array|max:5',
                'files.*' => 'max:' . $maxFileSize . '|mimes:' . implode(',', $allowedMimes)
            ]
        );

        $comment = DB::transaction(function () use ($request, $type, $files) {
            $_comment = TicketThread::create([
                'ticket_id' => $request->get('ticket_id'),
                'user_id' => Auth::id(),
                'type' => $type->value,
                'content' => $request->get('content')
            ]);
            if ($type === TicketThreadType::DECLINE_COMMENT || $type === TicketThreadType::APPROVE_COMMENT) {
                TicketParticipant
                    ::where('ticket_id', $_comment->ticket_id)
                    ->where('user_id', $_comment->user_id)
                    ->where('role', Participant::APPROVAL)
                    ->update(['approved' => $type === TicketThreadType::APPROVE_COMMENT]);
                $unApproved = TicketParticipant
                    ::where('ticket_id', 10009)
                    ->where('role', Participant::APPROVAL)
                    ->where(function (Builder $builder) {
                        $builder
                            ->orWhereNull('approved')
                            ->orWhere('approved', 0);
                    })->count();
                if ($unApproved === 0) {
                    Ticket::findOrFail($_comment->ticket_id)->update(['status' => TicketStatus::APPROVED]);
                } else {
                    Ticket::findOrFail($_comment->ticket_id)->update(['status' => TicketStatus::IN_APPROVAL]);
                }
            }
            if ($type === TicketThreadType::CLOSE_COMMENT) {
                Ticket::findOrFail($_comment->ticket_id)->update(['status' => TicketStatus::CLOSED]);
            }
            if ($type === TicketThreadType::REOPEN_COMMENT) {
                Ticket::findOrFail($_comment->ticket_id)->update(['status' => TicketStatus::IN_WORK]);
            }
            if ($type === TicketThreadType::SOLVED_COMMENT) {
                Ticket::findOrFail($_comment->ticket_id)->update(['status' => TicketStatus::SOLVED]);
            }

            if (is_array($files) && count($files) > 0) {
                foreach ($files as $file) {
                    try {
                        FileUploadHelper::uploadTicketThreadFile($_comment, $file);
                    } catch (\Exception $e) {
                        Log::error('[' . __METHOD__ . '] ' . $e->getMessage());
                    }

                }
            }
            $ticket = $_comment->ticket;
            $participants = $ticket->participants;
            Notification::send($participants, new NewCommentNotification($_comment));
            return $_comment->load('files');
        });
        return $comment;
    }

    public function editComment(int $id, Request $request)
    {

    }

    public function deleteComment(int $id)
    {

    }

    /**
     * @param int $commentId
     * @return null
     * @throws FilesystemException
     */
    public function getTicketThreadFiles(int $commentId)
    {
        return FileUploadHelper::downloadAllThreadFiles($commentId);
    }

    public function getTicketThreadFile(int $commentId, int $fileId): StreamedResponse
    {
        $file = TicketThreadCommentFile::findOrFail($fileId);
        return FileUploadHelper::downloadThreadFile($file);
    }
}
