<?php



namespace Http\Resources\City;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Framework\Core;

class CityResource extends JsonResource{


    public function toArray(Request $req){

        return [
            'id' => $this->City_Id,
            'test' => $this->country,
            'req' => $req,
            'user' => auth()->user()
        ];
    }

}