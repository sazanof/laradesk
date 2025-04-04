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
        return self::checkUserExistsInGroup($samAccountName, config('hd.ldap.admin_dn'));
    }

    /**
     * @param string $samAccountName
     * @return bool
     * @throws LdapEntityNotFountException
     */
    public static function isHelpdeskUser(string $samAccountName)
    {
        return self::checkUserExistsInGroup($samAccountName, config('hd.ldap.users.dn_group'));
    }

    /**
     * @param $samAccountName
     * @param $groupDn
     * @return boolean
     * @throws LdapEntityNotFountException
     */
    protected static function checkUserExistsInGroup($samAccountName, $groupDn): bool
    {

        try {
            $group = Group::find($groupDn);

        } catch (\Exception $exception) {
            dd($exception);
        }

        if (!is_null($group)) {
            $user = User::in(config('hd.ldap.users.dn'))->where('samaccountname', $samAccountName)->first();
            if (!is_null($user)) {
                return $user->groups()->recursive()->exists($group);
            }
        } else {
            throw  new LdapEntityNotFountException();
        }
        return false;
    }
}
