
<?php
require_once '../src/autoload.php';

Autoloader::register();

use Models\Permission;
use Framework\Facades\Auth;
use Framework\Core\Response;

$router = new \Framework\Routing\Router();
$router->setNamespace('\Http\Controllers');


$router->group('/auth', function () use ($router) {
    $router->get('/signin', 'AuthController@signIn');
    $router->post('/signin', 'AuthController@verifySignIn');
    $router->get('/register', 'AuthController@register');
    $router->post('/register', 'AuthController@verifyRegister');
});
$router->get('/logout', 'AuthController@logout');

$router->group('/city', function () use ($router) {
    $router->get('/show/{id}', 'CityController@show');
    $router->get('/add/{countryCode}', 'CityController@createCityView');
    $router->post('/add', 'CityController@create');
    $router->post('/update/{id}', 'CityController@update');
    $router->get('/delete/{id}', 'CityController@delete');
    $router->post('/search', 'CityController@search');
});

$router->group('/country', function () use ($router) {
    $router->get('/', 'CountryController@showAll');
    $router->get('/show/{id}', 'CountryController@show');
    $router->get('/add', 'CountryController@createCountryView');
    $router->post('/add', 'CountryController@create');
    $router->post('/update/{id}', 'CountryController@update');
    $router->get('/delete/{id}', 'CountryController@delete');
});
$router->get('/continent/{cont}', 'CountryController@findFromContinent');


$router->group('/admin', function () use ($router) {
    $router->get('/panel', 'AdminController@index');
    $router->post('/user/update/{id}', 'AdminController@updateUserRole');
});


$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
});



// //MiddleWares
// $router->before('GET|POST', '/admin/.*', function () {
//     if (!Auth::has(Permission::CANMANAGEUSERS)) {
//         header('location: /');
//         exit();
//     }
// });

// $router->before('GET|POST', '/auth/.*', function () {
//     if (Auth::has(Permission::CANMANAGEUSERS)) {
//         header('location: /admin/panel');
//         exit();
//     }
// });

$router->run();

function response() {
    return new Response();
}

?>