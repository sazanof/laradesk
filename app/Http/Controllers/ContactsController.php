<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function getContacts(Request $request)
    {
        $term = $request->get('term');
        $page = $request->get('page', 1);
        $q = User::query();
        if (!is_null($term)) {
            $q->where(function (Builder $query) use ($term) {
                $query->orWhere('firstname', 'LIKE', "%{$term}%");
                $query->orWhere('lastname', 'LIKE', "%{$term}%");
                $query->orWhere('email', 'LIKE', "%{$term}%");
                $query->orWhere('username', 'LIKE', "%{$term}%");
                $query->orWhere('phone', 'LIKE', "%{$term}%");
            });
        }
        $q->orderBy('lastname', 'ASC');
        return $q->paginate(50, '*', 'page', $page);
    }
}
