<?php

namespace App\Http\Middleware;

use App\Helpers\AclHelper;
use App\Models\Ticket;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserBelongsToDepartment
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $parameters = $request->route()->parameters();
        if (isset($parameters['id']) && (int)$parameters['id'] > 0) {
            // ticket page
            $id = (int)$parameters['id'];
            $departmentId = Ticket::find($id)->department_id;
        } else {
            $departmentId = $request->get('department');
        }

        if (!AclHelper::userHasDepartment($departmentId)) {
            throw new \Exception('You are not affiliated with any division. Contact your system administrator or try again later');
        }
        return $next($request);
    }
}
