<?php

namespace App\Http\Controllers;

use App\Helpers\SurmHelper;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class SurmApiController extends Controller
{
    public function getWorkplacesByUserRoom(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $room = $user->room;
        if ($room instanceof Room) {
            return SurmHelper::getWorkplacesByUser();
        }
        throw new \Exception('You has not room. Sorry.');
    }
}
