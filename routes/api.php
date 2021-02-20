<?php
use Framework\Routing\Router;

Router::group('/auth', function (){
    Router::get('/signin', 'AuthController@signIn');
    Router::post('/signin', 'AuthController@verifySignIn');
    Router::get('/register', 'AuthController@register');
    Router::post('/register', 'AuthController@verifyRegister');
});
Router::get('/logout', 'AuthController@logout');

Router::group('/city', function (){
    Router::get('/show/{id}', 'CityController@show');
    Router::get('/add/{countryCode}', 'CityController@createCityView');
    Router::post('/add', 'CityController@create');
    Router::post('/update/{id}', 'CityController@update');
    Router::get('/delete/{id}', 'CityController@delete');
    Router::post('/search', 'CityController@search');
});

Router::group('/country', function (){
    Router::get('/', 'CountryController@showAll');
    Router::get('/show/{id}', 'CountryController@show');
    Router::get('/add', 'CountryController@createCountryView');
    Router::post('/add', 'CountryController@create');
    Router::post('/update/{id}', 'CountryController@update');
    Router::get('/delete/{id}', 'CountryController@delete');
});
Router::get('/continent/{cont}', 'CountryController@findFromContinent');


Router::group('/admin', function (){
    Router::get('/panel', 'AdminController@index');
    Router::post('/user/update/{id}', 'AdminController@updateUserRole');
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
