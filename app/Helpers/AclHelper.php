<?php

namespace App\Helpers;

use App\Exceptions\LdapEntityNotFountException;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AclHelper
{
    const TICKETS = 'TICKETS';
    const SETTINGS = 'SETTINGS';
    const STATISTICS = 'STATISTICS';
    const USERS = 'USERS';

    const CREATE = 'C';
    const READ = 'R';
    const UPDATE = 'U';
    const DELETE = 'D';

    /**
     * @return string[]|null
     */
    public static function superAdminsFromEnv(): ?array
    {
        $env = env('HD_SUPER_ADMIN_IDS');
        if (!is_null($env)) {
            return explode(',', $env);
        }
        return null;
    }

    /**
     * @param User|null $user
     * @return bool
     * @throws LdapEntityNotFountException
     */
    public static function isSuperAdmin(User $user = null): bool
    {
        if (is_null($user)) {
            $user = Auth::user();
        }
        return LdapHelper::isHelpdeskAdmin($user->username) && in_array($user->id, self::superAdminsFromEnv());
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public static function isAdmin(User $user = null): bool
    {
        if (is_null($user)) {
            $user = Auth::user();
        }
        return $user->is_admin;
    }

    /**
     * @param string $entity
     * @param string $operation
     * @return bool|void
     * @throws LdapEntityNotFountException
     */
    public static function can(string $entity, string $operation)
    {
        if (self::isSuperAdmin()) {
            return true;
        }
    }

    /**
     * If user is requester
     * @param Ticket $ticket
     * @param null $userId
     * @return bool
     */
    public static function isRequester(Ticket $ticket, $userId = null): bool
    {
        if (is_null($userId)) {
            $userId = Auth::id();
        }
        return $ticket->user_id === $userId;
    }

    /**
     * If user in approvals list
     * @param Ticket $ticket
     * @param null $userId
     * @return bool
     */
    public static function isApproval(Ticket $ticket, $userId = null): bool
    {
        if (is_null($userId)) {
            $userId = Auth::id();
        }
        return $ticket->approvals()->where('user_id', $userId)->count() === 1;
    }

    /**
     * If user in observers list
     * @param Ticket $ticket
     * @param null $userId
     * @return bool
     */
    public static function isObserver(Ticket $ticket, $userId = null): bool
    {
        if (is_null($userId)) {
            $userId = Auth::id();
        }
        return $ticket->observers()->where('user_id', $userId)->count() === 1;
    }

    /**
     * If user in assignees list
     * @param Ticket $ticket
     * @param null $userId
     * @return bool
     */
    public static function isAssignee(Ticket $ticket, $userId = null): bool
    {
        if (is_null($userId)) {
            $userId = Auth::id();
        }
        return $ticket->assignees()->where('user_id', $userId)->count() === 1;
    }

    /**
     * if user can comment ticket
     * @param Ticket $ticket
     * @param $userId
     * @return bool
     */
    public static function userCanCommentTicket(Ticket $ticket, $userId = null)
    {
        if (is_null($userId)) {
            $userId = Auth::id();
        }
        return
            self::isAdmin(Auth::user()) ||
            self::isRequester($ticket, $userId) ||
            self::isApproval($ticket, $userId) ||
            self::isAssignee($ticket, $userId) ||
            self::isObserver($ticket, $userId);
    }
}
