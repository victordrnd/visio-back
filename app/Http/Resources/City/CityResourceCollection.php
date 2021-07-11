<?php



namespace Http\Resources\City;
use Framework\Core\Http\Request;
use Framework\Core\Http\Resources\JsonResource;
use Framework\Core\Http\Resources\ResourceCollection;

class CityResourceCollection extends ResourceCollection{


    public function toArray(Request $req){

        return $this->collection->map(function($el) {
            return (new CityResource($el));
        });
    }

}