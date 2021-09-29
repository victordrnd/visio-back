<?php

namespace Http\Controllers;

use Framework\Core\App;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Models\Room;
use Framework\Facades\Hash;

class RoomController extends Controller
{


    /**
     * Display city with specified id
     * 
     * @param int $id
     * @return void
     */
    public function show(int $id)
    {
        $user = Room::where('id', $id)->firstOrFail();
        return response()->json($user);
    }

    
    public function list(){
        return Room::all();
    }

    /**
     * Create a new user from form.
     *
     * @param Request $req
     * @return void
     */
    public function store(Request $req){
        $room = Room::create([
            'label' => $req->label,
            'picture' => $req->picture
        ]);
        return response()->json($room);
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
