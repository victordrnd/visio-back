<?php



namespace Http\Requests;
use Framework\Core\Http\Request;

class ShowCityRequest extends Request{


    public function rules(){

        return [
            'id' => ["required", "integer"]
        ];
    }

}