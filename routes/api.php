<?php
use Framework\Routing\Router;

Router::options('.*', function() {
   return "done";
});

Router::group('/auth', function (){
    Router::post('/register', 'AuthController@register');
    //Router::post('/register', 'AuthController@verifyRegister');
    Router::post('/login', 'AuthController@login');
    Router::get('/current', 'AuthController@current');
});



Router::group(["prefix" => '/users'], function(){
    Router::get('/{id}', 'UserController@show');
    Router::post('/', "UserController@store");
    Router::get('/my/rooms',    'UserController@rooms');
});

Router::group(["prefix" => '/my',] )


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
