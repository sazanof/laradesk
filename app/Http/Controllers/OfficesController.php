<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class OfficesController extends Controller
{
    /**
     * @return Collection
     */
    public function getOffices(): Collection
    {
        return Office::with('rooms')->orderBy('name')->get();
    }

    /**
     * @param Request $request
     * @return Office|Model
     */
    public function createOffice(Request $request): Model|Office
    {
        return Office::create($request->only(['name', 'address']));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Office|Office[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    public function editOffice(int $id, Request $request): Model|\Illuminate\Database\Eloquent\Collection|Office|array
    {
        $office = Office::findOrFail($id);
        $office->update($request->only(['name', 'address']));
        return $office;
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function deleteOffice(int $id): ?bool
    {
        return Office::findOrFail($id)->delete();
    }
}
