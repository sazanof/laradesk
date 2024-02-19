<?php

namespace App\Http\Controllers;

use App\Helpers\AclHelper;
use App\Helpers\DepartmentHelper;
use App\Models\AdminDepartments;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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
     * @return Department|Model
     */
    public function addDepartment(Request $request): Model|Department
    {
        return Department::create($request->only(['name', 'description']));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return bool
     */
    public function updateDepartment(int $id, Request $request): bool
    {
        $this->updateMembers($id, $request->get('members'));
        return Department::findOrFail($id)->update($request->only(['name', 'description']));
    }

    public function updateMembers(int $departmentId, array $members)
    {
        if (empty($members)) {
            AdminDepartments::whereDepartmentId($departmentId)->delete();
        } else {
            $currentAdminDepartmentIds = AdminDepartments::whereDepartmentId($departmentId);
            if ($currentAdminDepartmentIds->count() === 0) {
                foreach ($members as $userId) {
                    AdminDepartments::create(['department_id' => $departmentId, 'admin_id' => $userId]);
                    User::find($userId)->update(['is_admin' => true]);
                }
            } else {
                // take current records IDs in admin_department table
                $ids = explode(',', $currentAdminDepartmentIds->selectRaw('GROUP_CONCAT(admin_id) as ids')->firstOrFail()->ids);
                $ids = array_map(function ($el) {
                    return (int)$el;
                }, $ids);
                $add = array_diff($members, $ids);
                $delete = array_diff($ids, $members);
                if (!empty($add)) {
                    foreach ($add as $userId) {
                        AdminDepartments::create(['department_id' => $departmentId, 'admin_id' => $userId]);
                        User::find($userId)->update(['is_admin' => true]);
                    }
                }
                if (!empty($delete)) {
                    foreach ($delete as $userId) {
                        AdminDepartments::where(['department_id' => $departmentId])->where(['admin_id' => $userId])->delete();
                        if (AdminDepartments::whereAdminId($userId)->count() == 0) {
                            User::find($userId)->update(['is_admin' => false]);
                        }
                    }
                }

                // todo notifications

            }
        }
    }

    /**
     * @param int $id
     * @return void
     * @throws \Throwable
     */
    public function disableDepartment(int $id): void
    {
        Department::findOrFail($id)->deleteOrFail();
    }

    /**
     * @param int $id
     * @return void
     */
    public function enableDepartment(int $id): void
    {
        Department::withTrashed()->findOrFail($id)->restore();
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteDepartment(int $id): mixed
    {
        throw new \Exception('You can not delete department right now.');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getDepartmentMembers(int $id): mixed
    {
        return Department
            ::findOrFail($id)
            ->members()
            ->withTrashed()
            ->get();
    }
}
