<?php

namespace Http\Controllers;

use Framework\Core\App;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Models\Room;
use Framework\Facades\Hash;
use Models\UserRoom;

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
        $room = Room::where('id', $id)->with('messages')->firstOrFail();
        return response()->json($room);
    }

    
    public function list(){
        return Room::orderBy('updated_at', 'DESC')->get();
    }

    /**
     * Create a new user from form.
     *
     * @param Request $req
     * @return void
     */
    public function store(Request $req){
        $room = Room::create([
            'label' => null,
            'picture' => null
        ]);
        $req->user_ids[] = auth()->user()->id;
        foreach($req->user_ids as $user_id){
            UserRoom::create([
                'user_id' => $user_id,
                'room_id' => $room->id
            ]);
        }
        return $room;

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
