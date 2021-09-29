
<?php

use Framework\Core\App;
use Framework\Core\Http\Response;
use Framework\Facades\Auth;
use Framework\Routing\Router;
// header('Access-Control-Allow-Origin: http://localhost:4200');
// header('Content-Type: application/json');


// header("Access-Control-Allow-Headers: X-Requested-With");

header("Access-Control-Allow-Origin: http://localhost:4200");   
header("Content-Type: application/json; charset=UTF-8");    
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");    
header("Access-Control-Max-Age: 3600");    
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 
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