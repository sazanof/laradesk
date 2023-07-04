<?php

namespace App\Http\Middleware;

use App\Exceptions\LdapAccessDeniedException;
use App\Exceptions\LdapEntityNotFountException;
use App\Helpers\AclHelper;
use App\Helpers\LdapHelper;
use Closure;
use Illuminate\Http\Request;
use LdapRecord\LdapRecordException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class UserIsSuperAdmin
{
    /***
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws LdapAccessDeniedException
     * @throws LdapEntityNotFountException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException('Only xhr allowed');
        }
        try {
            if (!AclHelper::isSuperAdmin($request->user())) {
                throw new LdapAccessDeniedException();
            }
        } catch (LdapRecordException $e) {
            dd($e);
        }

        return $next($request);
    }
}
