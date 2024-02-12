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
     * @return array
     */
    public function changeDepartment($id, Request $request): array
    {
        $user = $request->user();
        if ($user instanceof User) {
            DepartmentHelper::setDepartment($user, $id);
            return ['id' => DepartmentHelper::getDepartment($user)];
        }
        return [];
    }
}
