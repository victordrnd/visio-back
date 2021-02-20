
<?php
use Framework\Core\Http\Response;
use Framework\Routing\Router;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
*/
require_once '../src/autoload.php';
Autoloader::register();




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




function response() {
    return new Response();
}

?>