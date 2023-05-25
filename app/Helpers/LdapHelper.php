<?php

namespace App\Helpers;

use App\Exceptions\LdapEntityNotFountException;
use App\Ldap\User;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\ActiveDirectory\Group;

class LdapHelper
{
    /**
     * @param string $samAccountName
     * @return bool
     * @throws LdapEntityNotFountException
     */
    public static function isHelpdeskAdmin(string $samAccountName)
    {
        return self::checkUserExistsInGroup($samAccountName, env('HD_ADMINISTRATORS_DN_GROUP'));
    }

    /**
     * @param string $samAccountName
     * @return bool
     * @throws LdapEntityNotFountException
     */
    public static function isHelpdeskUser(string $samAccountName)
    {
        return self::checkUserExistsInGroup($samAccountName, env('HD_USERS_DN_GROUP'));
    }

    /**
     * @param $samAccountName
     * @param $groupDn
     * @return boolean
     * @throws LdapEntityNotFountException
     */
    protected static function checkUserExistsInGroup($samAccountName, $groupDn): bool
    {
        $group = Group::find($groupDn);
        if (!is_null($group)) {
            $user = User::in(env('HD_USERS_DN'))->where('samaccountname', $samAccountName)->first();
            if (!is_null($user)) {
                return $user->groups()->recursive()->exists($group);
            }
        } else {
            throw  new LdapEntityNotFountException();
        }
        return false;
    }
}
