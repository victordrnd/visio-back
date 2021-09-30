<?php



namespace Http\Resources\User;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\ResourceCollection;

class UserResourceCollection extends ResourceCollection{

    public function toArray(Request $req){

        return $this->collection->map(function($el) {
            return (new UserResource($el));
        });
    }

}