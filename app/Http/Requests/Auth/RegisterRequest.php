<?php



namespace Http\Requests\Auth;
use Framework\Core\Http\Request;

class RegisterRequest extends Request{


    public function rules(){

        return [
            "firstname" => ['required', "string"],
            "lastname" => ["required", "string"],
            "email" => ["required", "string"],
            "password" => ["required", 'string']
        ];
    }

}