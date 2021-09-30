<?php

namespace Http\Controllers;

use Framework\Core\App;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Models\User;
use Framework\Facades\Hash;
use Http\Resources\User\UserResourceCollection;

class UserController extends Controller
{

    public function show(int $id)
    {
        $user = User::where('id', $id)->with('rooms')->firstOrFail();
        return response()->json($user);
    }



    public function rooms() : User{
        return auth()->user()->load('rooms');
    }


    

    public function list(){
        return new UserResourceCollection(User::all());
    }
}
