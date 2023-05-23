<?php

namespace App\Helpers;

use App\Exceptions\LdapEntityNotFountException;
use App\Ldap\User;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\ActiveDirectory\Group;

class LdapHelper
{
    /**
     * @param $samAccountName
     * @return bool
     * @throws LdapEntityNotFountException
     */
    public static function isHelpdeskSuperAdmin($samAccountName)
    {
        return self::isHelpdeskAdmin($samAccountName);//&& Auth::id();
    }

    /**
     * @param $samAccountName
     * @return bool
     * @throws LdapEntityNotFountException
     */
    public static function isHelpdeskAdmin($samAccountName)
    {
        return self::checkUserExistsInGroup($samAccountName, env('HD_ADMINISTRATORS_DN_GROUP'));
    }

    /**
     * @param $samAccountName
     * @return bool
     * @throws LdapEntityNotFountException
     */
    public static function isHelpdeskUser($samAccountName)
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
                return $user->groups()->exists($group);
            }
        } else {
            throw  new LdapEntityNotFountException();
        }
        return false;
    }
}
