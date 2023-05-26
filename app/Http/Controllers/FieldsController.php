<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FieldsController extends Controller
{
    /**
     * @return Field[]|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getAllFields(): Collection|array
    {
        return Field::withTrashed()
            ->orderBy('created_at')
            ->get();
    }

    public function getFields(): \Illuminate\Support\Collection
    {
        return Field::orderBy('created_at')
            ->get();
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Field|Field[]|Collection|Model
     */
    public function editField(int $id, Request $request): Model|Collection|array|Field
    {
        $field = Field::findOrFail($id);
        $field->update($request->all());
        return $field;
    }

    /**
     * @param Request $request
     * @return Field|Model
     */
    public function createField(Request $request): Model|Field
    {
        return Field::create($request->all());
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function deleteField(int $id): ?bool
    {
        return Field::findOrFail($id)->delete();
    }
}
