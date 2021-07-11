<?php

namespace Framework\Core;

use Framework\Core\Http\Request;



class App {

    private static $request = null;

    public function __construct() {
        
    }


    public static function request() {
        return self::$request;
    }


    public static function setRequest($req) {
        self::$request = $req;
    }
}
