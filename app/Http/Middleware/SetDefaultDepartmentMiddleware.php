<?php

namespace App\Http\Middleware;

use App\Helpers\DepartmentHelper;
use App\Models\AdminDepartments;
use App\Models\Department;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SetDefaultDepartmentMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();
        if (!is_null($user)) {
            $d = DepartmentHelper::getDepartment($user);
            $deps = AdminDepartments::where('admin_id', $user->id);
            if ($deps->count() > 0 && $d === null) {
                DepartmentHelper::setDepartment($user, $deps->first()->department_id);
            }
        }

        return $next($request);
    }
}
