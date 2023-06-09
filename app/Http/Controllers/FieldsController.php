<?php

namespace App\Http\Controllers;

use App\Helpdesk\TicketsStorage;
use App\Helpers\FieldHelper;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Ticket;
use App\Models\TicketFields;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @throws \Throwable
     */
    public function deleteField(int $id): ?bool
    {
        return DB::transaction(function () use ($id) {
            Field::findOrFail($id)->delete();
            FieldCategory::where('field_id', $id)->delete();
            return true;
        });
    }

    public function linkField(Request $request)
    {
        $request->validate([
            'field_id' => 'required',
            'category_id' => 'required',
        ]);
        $data = $request->only('field_id', 'category_id', 'order');
        return FieldCategory::create($data);
    }

    public function unlinkField(Request $request)
    {
        $request->validate([
            'field_id' => 'required',
            'category_id' => 'required',
        ]);
        $data = $request->only('field_id', 'category_id');
        return FieldCategory::where('field_id', $data['field_id'])
            ->where('category_id', $data['category_id'])
            ->delete();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function changeFieldOrder(Request $request): bool
    {
        $id = $request->get('id');
        $order = $request->get('order');
        return FieldCategory::findOrFail($id)->update(['order' => $order]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function makeFieldRequired(Request $request): bool
    {
        $id = $request->get('id');
        $order = $request->get('required');
        return FieldCategory::findOrFail($id)->update(['required' => $order]);
    }

    public function getFile($id)
    {
        $ticketField = TicketFields::find($id);
        if ($ticketField->field->type === FieldHelper::TYPE_FILE) {
            TicketsStorage::getFile($ticketField);
        }
    }

    public function downloadFiles(int $id)
    {
        TicketsStorage::downloadAllFiles($id);
    }
}
