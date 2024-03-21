<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class User extends \LdapRecord\Models\ActiveDirectory\User
{
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static array $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'user',
    ];
}
