<?php



namespace Http\Resources\Room;

use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Framework\Core;
use Models\User;
use Models\UserRoom;

class RoomResource extends JsonResource
{

    public function toArray(Request $req)
    {
        $users_ids = UserRoom::where('room_id', $this->id)->get()->pluck('user_id');
        return [
            'id' => $this->id,
            'label' => $this->firstname,
            'picture' => $this->picture,
            'users' => User::whereIn('id',UserRoom::where('room_id', $this->id)->get()->pluck('user_id'))->get(),
            'user_room' => $users_ids,
            'last_message' => $this->last_message,
            'messages' => $this->whenLoaded('messages', function(){
                return $this->messages;
            })
        ];
    }
}
