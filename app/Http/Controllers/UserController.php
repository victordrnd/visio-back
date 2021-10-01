<?php

namespace Http\Controllers;

use Models\User;
use Http\Resources\User\UserResourceCollection;
use Models\Room;

class UserController extends Controller
{

    public function show(int $id)
    {
        $user = User::where('id', $id)->with('rooms')->firstOrFail();
        return response()->json($user);
    }



    public function rooms() {
        return Room::whereHas('user_rooms', function($query) {
            var_dump($query);
            return $query;
        })->get();

    }


    

    public function list(){
        return new UserResourceCollection(User::all());
    }
}
