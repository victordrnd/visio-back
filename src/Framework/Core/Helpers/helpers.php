<?php

use Framework\Core\App;
use Framework\Core\Http\Request;
use Framework\Core\Http\Response;
use Framework\Facades\Auth;

if (!function_exists('response')) {
    function response() {
        return new Response();
    }
}

if(!function_exists('request')){
    function request() : Request{
        return App::request();
    }
}

if(!function_exists('auth')){
    function auth(){
        return new Auth;
    }
}
