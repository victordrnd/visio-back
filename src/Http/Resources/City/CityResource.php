<?php



namespace Http\Resources\City;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;

class CityResource extends JsonResource{


    public function toArray(Request $req){

        return [
            'id' => $this->City_Id,
            'test' => $this->country,
            'req' => $req
        ];
    }

}