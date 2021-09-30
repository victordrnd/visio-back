<?php

namespace Framework\Facades;

class Cors
{

    public static function registerCors()
    {
        $cors_options = include $_SERVER['DOCUMENT_ROOT'] . "/config/cors.php";
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
            header("HTTP/1.1 200 OK");
            return;
        }
        foreach($cors_options as $cors_option => $value){
            header("$cors_option : $value");
        }
    }
}
