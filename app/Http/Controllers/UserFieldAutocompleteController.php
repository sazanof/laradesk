<?php

namespace App\Http\Controllers;

use App\Models\UserFieldAutocomplete;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserFieldAutocompleteController extends Controller
{
    /**
     * @param Request $request
     * @return UserFieldAutocomplete|void
     * @throws \Exception
     */
    public function add(Request $request)
    {
        $fields = $request->only(['field_id', 'value']);
        if ($request->validate([
            'field_id' => 'required',
            'value' => 'required'
        ])) {
            $existing = UserFieldAutocomplete
                ::where('user_id', Auth::id())
                ->where('field_id', $fields['field_id'])->whereRaw('LOWER(value) = ?', $fields['value'])->get();
            if ($existing->count() > 0) {
                throw new \Exception('Can not add duplicated autocomplete value');
            }
            return UserFieldAutocomplete::create(
                array_merge(
                    [
                        'user_id' => Auth::id()
                    ],
                    $fields
                )
            );
        }

    }

    public function save()
    {

    }

    /**
     * @param int $id
     * @return void
     * @throws \Throwable
     */
    public function delete(int $id): void
    {
        $founded = UserFieldAutocomplete::findOrFail($id);
        if ($founded->user_id == Auth::id()) {
            $founded->deleteOrFail();
        }
    }

    /**
     * @return UserFieldAutocomplete[]|Collection|null
     */
    public function list(int $id, Request $request)
    {
        $res = UserFieldAutocomplete::query();
        $term = $request->get('term');
        $res
            ->where('field_id', $id)
            ->where('user_id', Auth::id())
            ->orderBy('value')
            ->limit(20);
        if (is_string($term) && Str::length($term) > 0) {
            $res
                ->where('value', 'LIKE', '%' . $term . '%');
        }
        return $res->get();
    }
}
