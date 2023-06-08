<?php

namespace App\Helpers;

use App\Exceptions\LdapEntityNotFountException;
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
}
