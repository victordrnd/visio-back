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


// Router::group(['prefix' => '/users', 'middleware' => 'auth:api'], function(){
//     Router::get("/", "AuthController@getAll");
// });

Router::group(["prefix" => '/users'], function(){
    Router::get('/{id}', 'UserController@show');
    Router::post('/', "UserController@store");
});



Router::group('/city', function (){
    Router::get('/show/{id}', 'CityController@show');
    Router::post('/add', 'CityController@create');
    Router::post('/update/{id}', 'CityController@update');
    Router::get('/delete/{id}', 'CityController@delete');
    Router::post('/search', 'CityController@search');
});

Router::group('/country', function (){
    Router::get('/', 'CountryController@showAll');
    Router::get('/show/{id}', 'CountryController@show');
    Router::post('/add', 'CountryController@create');
    Router::post('/update/{id}', 'CountryController@update');
    Router::get('/delete/{id}', 'CountryController@delete');
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
