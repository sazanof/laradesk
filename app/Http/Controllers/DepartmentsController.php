<?php

namespace App\Http\Controllers;

use App\Helpers\AclHelper;
use App\Helpers\DepartmentHelper;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DepartmentsController extends Controller
{
    /**
     * @return Collection
     */
    public function getDepartments(): Collection
    {
        if (AclHelper::isSuperAdmin()) {
            return Department
                ::withTrashed()
                ->select(['id', 'name', 'description', 'deleted_at'])
                ->orderBy('name', 'ASC')
                ->with('categories')
                ->get();
        }
        return Department
            ::select(['id', 'name', 'description'])
            ->orderBy('name', 'ASC')
            ->with('categories')
            ->get();
    }

    /**
     * @param $id
     * @param Request $request
     * @return array|null
     */
    public function changeDepartment($id, Request $request)
    {
        $user = $request->user();
        if ($user instanceof User) {
            /** @var Department $dep */
            $dep = DepartmentHelper::setDepartment($user, $id)->department;
            return $dep->only(['id', 'name', 'description']);
        }
        return null;
    }

    /**
     * @param Request $request
     * @return Department|\Illuminate\Database\Eloquent\Model
     */
    public function addDepartment(Request $request)
    {
        return Department::create($request->only(['name', 'description']));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return bool
     */
    public function updateDepartment(int $id, Request $request)
    {
        return Department::findOrFail($id)->update($request->only(['name', 'description']));
    }

    /**
     * @param int $id
     * @return void
     * @throws \Throwable
     */
    public function disableDepartment(int $id)
    {
        Department::findOrFail($id)->deleteOrFail();
    }

    /**
     * @param int $id
     * @return void
     */
    public function enableDepartment(int $id)
    {
        Department::withTrashed()->findOrFail($id)->restore();
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteDepartment(int $id)
    {
        throw new \Exception('You can not delete department right now.');
    }
}
