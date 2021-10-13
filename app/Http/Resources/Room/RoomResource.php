<?php



namespace Http\Resources\Room;

use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Framework\Core;
use Models\UserRoom;

class RoomResource extends JsonResource
{

    public function toArray(Request $req)
    {

        return [
            'id' => $this->id,
            'label' => $this->label,
            'picture' => $this->picture,
            'users' => $this->users,//UserRoom::where('room_id', $this->id)->get(),
            'messages' => $this->messages
        ];
    }
}
