
<?php

use Framework\Core\App;
use Framework\Core\Http\Response;
use Framework\Facades\Auth;
use Framework\Facades\Cors;
use Framework\Routing\Router;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
*/
require_once '../app/autoload.php';
require_once "../app/Framework/Core/Helpers/helpers.php";


Autoloader::register();

/*
|--------------------------------------------------------------------------
| Register Cors options
|--------------------------------------------------------------------------
|
*/
Cors::registerCors();


/*
|--------------------------------------------------------------------------
| Register The Router and load routes
|--------------------------------------------------------------------------
|
*/
Router::group('/api', function(){
    require_once '../routes/api.php';
});
Router::run();



$app = new App;


?>