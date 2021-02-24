<?php



namespace Http\Requests;
use Framework\Core\Http\Request;

class CreateCityRequest extends Request{


    public function rules(){

        return [
            'name' => ["required", "string"]
        ];
    }

}