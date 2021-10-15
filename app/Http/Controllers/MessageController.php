<?php

namespace Http\Controllers;

use Framework\Core\App;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Models\Room;
use Framework\Facades\Hash;
use Models\Message;

class MessageController extends Controller
{


    /**
     * Create a new message
     *
     * @param Request $req
     * @return void
     */
    public function store(Request $req){
        $message = Message::create([
            'type' => $req->type,
            'message' => $req->message,
            'user_id' => auth()->user()->id,
            'room_id' => $req->room_id
        ]);
        $room = Room::find($req->room_id);
        $room->updated_at =  date("Y-m-d H:i:s");
        $room->save();
        return response()->json($message);
    }

  
    
    public function delete(int $id)
    {
       return response()->json(Message::destroy($id), 200);
    }
}
