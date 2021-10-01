<?php



namespace Http\Resources\Room;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\ResourceCollection;

class RoomResourceCollection extends ResourceCollection{

    public function toArray(Request $req){

        return $this->collection->map(function($el) {
            return (new RoomResource($el));
        });
    }

}