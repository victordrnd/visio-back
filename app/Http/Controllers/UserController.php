<?php

namespace Http\Controllers;

use Framework\Core\App;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Models\User;
use Framework\Facades\Hash;

class UserController extends Controller
{


    /**
     * Display city with specified id
     * 
     * @param int $id
     * @return void
     */
    public function show(int $id)
    {
        $user = User::where('id', $id)->with('rooms')->firstOrFail();
        return response()->json($user);
    }


    /**
     * Create a new user from form.
     *
     * @param Request $req
     * @return void
     */
    public function store(Request $req){
        $user = User::create([
            'firstname' =>"Victor",
            'lastname' => "Durand",
            'email' => "vic20016@gmail.com",
            'password' => Hash::make("test")
        ]);
        return response()->json($user);
    }

    /**
     * Update city with specified id
     *
     * @param Request $req
     * @param integer $id
     * @return void
     */
    public function update(Request $req, int $id)
    {
       
    }


    /**
     * Delete City with specified id
     *
     * @param [type] $id
     * @return void
     */
    public function delete(int $id)
    {
       
    }
}
