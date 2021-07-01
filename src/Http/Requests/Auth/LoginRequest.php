<?php



namespace Http\Requests\Auth;
use Framework\Core\Http\Request;

class LoginRequest extends Request{


    public function rules(){

        return [
            'login' => ["required", "string"],
            'password' => ["required", 'string']
        ];
    }

}