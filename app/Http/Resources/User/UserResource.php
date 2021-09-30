<?php



namespace Http\Resources\User;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Framework\Core;

class UserResource extends JsonResource{


    public function toArray(Request $req){

        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
        ];
    }

}