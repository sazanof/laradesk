<?php

namespace App\Ldap\Scopes;

use LdapRecord\Models\Scope;
use LdapRecord\Models\Model;
use LdapRecord\Query\Model\Builder;

class UserScope implements Scope
{
    /**
     * Apply the scope to the query.
     *
     * @param Builder $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('company', '=', 'Acme Company');
    }
}
