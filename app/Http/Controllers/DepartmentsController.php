<?php

namespace App\Http\Controllers;

use App\Models\Department;
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
}
