<?php

namespace Framework\Routing;

use Exception;
use Framework\Core\App;
use Framework\Core\Http\Request;
use Framework\Core\Resolver;
use Framework\Core\Http\Response;

class Router {

    private static $afterRoutes = [];


    private static $beforeRoutes = [];


    protected static $notFoundCallback;


    private static $baseRoute = '';


    private static $requestedMethod = '';


    private static $serverBasePath;


    private static $namespace = '\Http\Controllers';


    /**
     * Undocumented function
     *
     * @param [type] $methods
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function before($methods, $pattern, \Closure $fn): void {
        $pattern = static::$baseRoute . '/' . trim($pattern, '/');
        $pattern = static::$baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            static::$beforeRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn,
            ];
        }
    }


    /**
     * Undocumented function
     *
     * @param [type] $methods
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function match($methods, $pattern, $fn): void {
        $pattern = static::$baseRoute . '/' . trim($pattern, '/');
        $pattern = static::$baseRoute ? rtrim($pattern, '/') : $pattern;
        foreach (explode('|', $methods) as $method) {
            static::$afterRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn,
            ];
        }
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function all($pattern,  $fn): void {
        self::match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function get($pattern, $fn): void {
        self::match('GET', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function post($pattern, $fn): void {
        self::match('POST', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function patch($pattern, \Closure $fn): void {
        self::match('PATCH', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function delete($pattern, $fn): void {
        self::match('DELETE', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function put($pattern, $fn): void {
        self::match('PUT', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public static function options($pattern, $fn): void {
        self::match('OPTIONS', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param string $baseRoute
     * @param callback $fn
     * @return void
     */
    public static function group($baseRoute, \Closure $fn) {
        if (is_string($baseRoute)) {
            $curBaseRoute = static::$baseRoute;
            static::$baseRoute .= $baseRoute;
            call_user_func($fn);
            static::$baseRoute = $curBaseRoute;
        } elseif (is_array($baseRoute)) {
            if (isset($baseRoute['prefix'])) {
                self::group($baseRoute['prefix'], $fn);
                if (isset($baseRoute['middleware'])) {
                    self::before('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $baseRoute['prefix'].".*", function() use ($baseRoute){
                        $middlewares = include $_SERVER['DOCUMENT_ROOT'] . "/config/middlewares.php";
                        self::call_class_function($middlewares[$baseRoute["middleware"]], "handle");
                    });
                }
            }
        }
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public static function getRequestHeaders() {
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if ($headers !== false) {
                return $headers;
            }
        }
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace([' ', 'Http'], ['-', 'HTTP'], ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getRequestMethod(): string {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = self::getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH'])) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }
        return $method;
    }

    /**
     * Undocumented function
     *
     * @param [type] $namespace
     * @return void
     */
    public static function setNamespace(string $namespace) {
        if (is_string($namespace)) {
            static::$namespace = $namespace;
        }
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getNamespace(): string {
        return static::$namespace;
    }


    /**
     * Undocumented function
     *
     * @param [type] $callback
     * @return void
     */
    public static function run($callback = null) {
        static::$requestedMethod = self::getRequestMethod();
        if (isset(static::$beforeRoutes[static::$requestedMethod])) {
            self::handle(static::$beforeRoutes[static::$requestedMethod]);
        }
        $numHandled = 0;
        if (isset(static::$afterRoutes[static::$requestedMethod])) {
            $numHandled = self::handle(static::$afterRoutes[static::$requestedMethod], true);
        }
        if ($numHandled === 0) {
            if (static::$notFoundCallback) {
                self::invoke(static::$notFoundCallback);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        } else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }
        return $numHandled !== 0;
    }


    /**
     * Undocumented function
     *
     * @param [type] $fn
     * @return void
     */
    public static function set404($fn): void {
        static::$notFoundCallback = $fn;
    }


    /**
     * Undocumented function
     *
     * @param [type] $routes
     * @param boolean $quitAfterRun
     * @return void
     */
    private static function handle($routes, $quitAfterRun = false) {
        $numHandled = 0;
        $uri = self::getCurrentUri();
        foreach ($routes as $route) {
            $route['pattern'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['pattern']);
            if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                $matches = array_slice($matches, 1);
                $params = array_map(function ($match, $index) use ($matches) {
                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                    }
                    return isset($match[0][0]) ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));
                self::invoke($route['fn'], $params);
                ++$numHandled;
                if ($quitAfterRun) {
                    break;
                }
            }
        }


        return $numHandled;
    }

    /**
     * Call the controller and inject dependencies automatically if needed (via Resolver)
     *
     * @param [type] $fn
     * @param array $params
     * @return void
     */
    private static function invoke($fn, $params = []): void {
        if (is_callable($fn)) {
            $function_result = call_user_func_array($fn, $params);
            if ($function_result instanceof Response) {
                echo $function_result->finalize();
            }
        } elseif (stripos($fn, '@') !== false) {
            list($controller, $method) = explode('@', $fn);
            if (self::getNamespace() !== '') {
                $controller = self::getNamespace() . '\\' . $controller;
            }
            if (class_exists($controller)) {
                self::call_class_function($controller, $method, $params);
            }
        }
    }

    private static function call_class_function($class, $method, $params = []) {
        $additionnalparams = Resolver::resolveFunction($class, $method);
        $params = array_merge($additionnalparams, $params);
        foreach($params as $param){
            if($param instanceof Request){
                $errors = $param->validate();
                if(is_array($errors)){
                    echo response()->json(['errors' => $errors], 401)->finalize();die;
                }
                App::setRequest($param);
                break;
            }
        }
        if(is_null(App::request())){
            App::setRequest(new Request);
        }
        try{
            $function_result = call_user_func_array([Resolver::resolve($class), $method], $params);
            if(!preg_match("/Middleware/i", $class)){
                if ($function_result === false) {
                    if ($function_result = forward_static_call_array([$class, $method], $params) === false);
                }
                if ($function_result instanceof Response) {
                    echo $function_result->finalize();
                    die;
                }else{
                    echo (new Response($function_result))->finalize();
                    die;
                }
            }
        }catch(\Exception $e){
            echo response()->json(['title' => get_class($e), 'error' => $e->getMessage()],$e->getCode())->finalize();
            die;
        }
    }


    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getCurrentUri(): string {
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen(self::getBasePath()));
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/' . trim($uri, '/');
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public static function getBasePath() {
        if (static::$serverBasePath === null) {
            static::$serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }
        return static::$serverBasePath;
    }


    /**
     * Undocumented function
     *
     * @param [type] $serverBasePath
     * @return void
     */
    public static function setBasePath($serverBasePath): void {
        static::$serverBasePath = $serverBasePath;
    }
}
