<?php

namespace App\Http\Controllers;

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
}
