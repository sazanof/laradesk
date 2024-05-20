<?php

namespace App\Http\Controllers;

use App\Events\NewParticipant;
use App\Helpdesk\TicketFromRequest;
use App\Helpdesk\Participant;
use App\Helpdesk\TicketStatus;
use App\Helpers\AclHelper;
use App\Helpers\DepartmentHelper;
use App\Helpers\FileUploadHelper;
use App\Helpers\RequestBuilder;
use App\Models\Ticket;
use App\Models\TicketFields;
use App\Models\TicketParticipant;
use App\Models\TicketThread;
use App\Models\User;
use App\Notifications\NewTicketNotification;
use App\Notifications\NewTicketParticipantNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
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
        Notification::send(
            Participant::getAdministrators($t->department_id),
            new NewTicketNotification($t)
        );
        return $t->only('id');
    }

    public function uploadImageInEditor(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $image = $request->file('image');
        $storage = FileUploadHelper::uploadedImagesStorage();
        $subfolder = md5($user->id . '_' . Carbon::now()->format('Y-m-d'));
        if (!$storage->directoryExists($subfolder)) {
            $storage->createDirectory($subfolder);
        }
        $filename = $subfolder . DIRECTORY_SEPARATOR . uniqid('img_') . '.' . $image->getClientOriginalExtension();
        // dd($filename);
        if ($storage->put(
            $filename,
            $image->getContent())) {
            return [
                'url' => $storage->url($filename)
            ];
        }
        throw new UploadException('Error uploading image in ticket content');
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
            ->whereIn('status', TicketStatus::OPEN)
            ->onlyByRoleAndUserId(Participant::ASSIGNEE, $user->id)
            ->count();

        $approval = Ticket::select(['tickets.*'])
            ->whereNotIn('tickets.status', [TicketStatus::SOLVED, TicketStatus::CLOSED, TicketStatus::APPROVED])
            ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
            ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id')
            ->where('tp.role', Participant::APPROVAL)
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
     * @return Ticket|Ticket[]|Collection|Model|null
     */
    public function getTicket(int $id): Model|Collection|Ticket|array|null
    {
        return Ticket::find($id)->load([
            'fields',
            'files',
            'department',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals',
            'office',
            'room'
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
            TicketParticipant::withTrashed()->where('ticket_id', $ticket->id)->delete();
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
            TicketParticipant::withTrashed()->where('ticket_id', $ticket->id)->restore();
            TicketThread::withTrashed()->where('ticket_id', $ticket->id)->restore();
            TicketFields::withTrashed()->where('ticket_id', $ticket->id)->restore();
        }
    }

    /**
     * @param int $id
     * @return Model|Collection|Ticket|array|null
     */
    public function getUserTicket(int $id): Model|Collection|Ticket|array|null
    {
        $ticket = Ticket::find($id)->load([
            'fields',
            'files',
            'department',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals',
            'office',
            'room'
        ]);
        if ($ticket->approvals()->where('user_id', Auth::id())->count() === 1) {
            return $ticket;
        }
        if ($ticket->observers()->where('user_id', Auth::id())->count() === 1) {
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
                $participant = TicketParticipant
                    ::withTrashed()
                    ->where('user_id', $uid)
                    ->where('ticket_id', $ticket->id)
                    ->where('role', $type)->first();
                if (!empty($participant)) {
                    if ($participant->trashed()) {
                        $participant->restore();
                    }
                } else {
                    $participant = TicketParticipant::updateOrCreate([
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
                $ticket->refresh();
                Notification::send(
                    $ticket->participants,
                    new NewTicketParticipantNotification($participant, $request->user())
                );

            }


        } catch (QueryException $exception) {
            // TODO - maybe delete this, case added upper if ($participant->trashed()) {...}
            /*TicketParticipant::withTrashed()->whereIn('user_id', $user_ids)
                ->where('ticket_id', $ticket->id)
                ->where('role', $type)
                ->restore();*/
            throw $exception;
        }
        $ticket->status = TicketStatus::IN_WORK;
        $ticket->save();
        $ticket->refresh();
        // Get only newly participants
        /** @var TicketParticipant $p */
        return match ($type) {
            Participant::ASSIGNEE => $ticket->assignees,
            Participant::APPROVAL => $ticket->approvals,
            Participant::OBSERVER => $ticket->observers,
            default => [],
        };

    }

    public function removeParticipant(int $id, Request $request)
    {
        $ticket = Ticket::find($id);
        if (($ticket->user_id !== Auth::id()) && !$request->user()->is_admin) {
            throw new \Exception('You can not add participants.');
        }
        $participantUserId = $request->get('id');
        $type = $request->get('type');
        TicketParticipant::find($participantUserId)->delete();
        switch ($type) {
            case Participant::OBSERVER:
                return $ticket->observers;
            case Participant::APPROVAL:
                return $ticket->approvals;
            case Participant::ASSIGNEE:
                return $ticket->assignees;
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @return BinaryFileResponse|null
     */
    public function getTicketFiles(int $id): ?BinaryFileResponse
    {
        return FileUploadHelper::downloadAllTicketFiles($id);
    }

    public function getTicketFile(int $id, string $path, Request $request)
    {
        $filename = $id . DIRECTORY_SEPARATOR . $path;
        $storage = FileUploadHelper::ticketFilesStorage();
        if ($storage->exists($filename)) {
            return $storage->download($filename);
        }
        throw new FileNotFoundException('Ticket file not found');
    }
}
