<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUser(): User|Authenticatable|null
    {
        return Auth::check() ? Auth::user() : null;
    }
}
