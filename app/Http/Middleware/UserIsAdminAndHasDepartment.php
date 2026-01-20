<?php

namespace App\Http\Middleware;

use App\Exceptions\LdapAccessDeniedException;
use App\Helpers\AclHelper;
use App\Helpers\LdapHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class UserIsAdminAndHasDepartment
{
    /***
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws LdapAccessDeniedException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException('Only xhr allowed');
        }
        if (!AclHelper::isAdmin($request->user())) {
            throw new LdapAccessDeniedException();
        }
        $departmentId = $request->get('department_id');
        if ($user instanceof User) {
            if ($user->departments->where('department_id', $departmentId)->count() > 0) {
                return $next($request);
            }
        }
        throw new LdapAccessDeniedException();

    }
}
