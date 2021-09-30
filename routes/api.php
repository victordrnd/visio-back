<?php

use Framework\Routing\Router;

Router::group('/auth', function () {
    Router::post('/register', 'AuthController@register');
    Router::post('/login', 'AuthController@login');
    Router::get('/current', 'AuthController@current');
});



Router::group(["prefix" => '/users'], function () {
    Router::get('/',            'UserController@list');
    Router::get('/{id}',        'UserController@show');
    Router::post('/',           "UserController@store");
});


// Router::group(["prefix" => 'rooms'], function(){
//     Router::get('/{id}',        'RoomController@show');
// });

Router::group(["prefix" => '/my', "middleware" => "auth:api"], function () {
    Router::get('/rooms',   'UserController@rooms');
});


Router::set404(function () {
    return response()->json(["Error" => "404 Not found"], 404);
});




// //MiddleWares
// Router::before('GET|POST', '/admin/.*', function () {
//     if (!Auth::has(Permission::CANMANAGEUSERS)) {
//         header('location: /');
//         exit();
//     }
// });

// Router::before('GET|POST', '/auth/.*', function () {
//     if (Auth::has(Permission::CANMANAGEUSERS)) {
//         header('location: /admin/panel');
//         exit();
//     }
// });
