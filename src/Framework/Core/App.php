<?php

namespace Framework\Core;
use Framework\Core\Http\Request;

class App {

    private static $request = null;

    public static function request(){
        return self::$request;
    }

    public static function setRequest(Request $req){
        self::$request = $req;
    }
}
