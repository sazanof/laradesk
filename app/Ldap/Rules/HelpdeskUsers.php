<?php

namespace App\Ldap\Rules;

use Illuminate\Database\Eloquent\Model as Eloquent;
use LdapRecord\Laravel\Auth\Rule;
use LdapRecord\Models\ActiveDirectory\Group;
use LdapRecord\Models\Model as LdapRecord;

class HelpdeskUsers implements Rule
{

    public function passes(LdapRecord $user, Eloquent $model = null): bool
    {
        $users = Group::find(env('HD_USERS_DN_GROUP'));

        return $user->groups()->recursive()->exists($users);
    }
}
