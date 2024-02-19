<?php

namespace App\Http\Middleware;

use App\Helpers\AclHelper;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserHasAccessToTicketMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();
        $id = $request->route('id');
        $commentId = $request->route('commentId');
        $ticket = null;
        if ($id || $commentId) {
            // Find by route {id}
            if ($id) {
                $ticket = Ticket::findOrFail($request->route('id'));
            } // find by route {commentId}
            else if ($commentId) {
                $comment = TicketThread::findOrFail($commentId);
                $ticket = Ticket::findOrFail($comment->ticket_id);
            }

            if ($ticket) {
                if (AclHelper::userHasAccessToTicket($ticket, $user)) {
                    return $next($request);
                }
            }
        }
        throw new \Exception('User has no access to ticket');
    }
}
