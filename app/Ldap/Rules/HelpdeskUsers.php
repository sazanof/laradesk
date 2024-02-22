<?php

namespace App\Ldap\Rules;

use LdapRecord\Laravel\Auth\Rule;
use LdapRecord\Models\ActiveDirectory\Group;

class HelpdeskUsers extends Rule
{
    /**
     * Check if the rule passes validation.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $users = Group::find(env('HD_USERS_DN_GROUP'));

        return $this->user->groups()->recursive()->exists($users);
    }
}
