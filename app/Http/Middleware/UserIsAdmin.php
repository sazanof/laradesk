<?php

namespace App\Http\Middleware;

use App\Exceptions\LdapAccessDeniedException;
use App\Helpers\AclHelper;
use App\Helpers\LdapHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class UserIsAdmin
{
    /***
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws LdapAccessDeniedException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException('Only xhr allowed');
        }
        if (!AclHelper::isAdmin($request->user())) {
            throw new LdapAccessDeniedException();
        }
        return $next($request);
    }
}
