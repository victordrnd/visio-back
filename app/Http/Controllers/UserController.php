<?php

namespace Http\Controllers;

use Models\User;
use Http\Resources\User\UserResourceCollection;
use Models\Room;
use Models\UserRoom;

class UserController extends Controller
{

    public function show(int $id)
    {
        $user = User::where('id', $id)->with('rooms')->firstOrFail();
        return response()->json($user);
    }



    public function rooms()
    {
        return Room::whereIn(
            "id",
            UserRoom::where('user_id', auth()->user()->id)->get()->pluck('room_id')
        )->with('last_message')->orderBy('updated_at', 'desc')->get();
    }




    public function list()
    {
        return new UserResourceCollection(User::all());
    }
}
