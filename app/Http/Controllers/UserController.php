<?php

namespace App\Http\Controllers;

use App\Exceptions\LdapAccessDeniedException;
use App\Exceptions\LdapEntityNotFountException;
use App\Helpers\AclHelper;
use App\Helpers\LdapHelper;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUser(): User|Authenticatable|null
    {
        return Auth::check() ? Auth::user() : null;
    }

    /**
     * @throws LdapEntityNotFountException
     * @throws AuthenticationException
     * @throws LdapAccessDeniedException
     */
    public function authUser(Request $request)
    {
        $credentials = [
            'samaccountname' => $request->get('username'),
            'password' => $request->get('password'),
        ];
        if (LdapHelper::isHelpdeskUser($request->get('username'))) {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $user->is_admin = LdapHelper::isHelpdeskAdmin($user->username);
                $user->is_super_admin = AclHelper::isSuperAdmin();
                $user->save();
                return $user;
            } else {
                throw new AuthenticationException();
            }
        } else {
            throw new LdapAccessDeniedException();
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }

    /**
     * @param Request $request
     * @return User|Authenticatable|null
     */
    public function editProfile(Request $request): User|Authenticatable|null
    {
        $user = Auth::user();
        $user->update($request->all());
        return $user;
    }

    public function searchUsers($term)
    {
        return User::select(['id', 'photo', 'email', 'firstname', 'lastname', 'position', 'department', 'organization'])->where(function (Builder $builder) use ($term) {
            $builder->orWhere('email', 'LIKE', $term . "%");
            $builder->orWhere('firstname', 'LIKE', $term . "%");
            $builder->orWhere('lastname', 'LIKE', $term . "%");
        })->get();
    }
}
