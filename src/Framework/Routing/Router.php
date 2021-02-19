<?php
namespace Framework\Routing;
use Framework\Core\Resolver;
use Framework\Core\Response;

class Router
{

    private $afterRoutes = [];


    private $beforeRoutes = [];


    protected $notFoundCallback;


    private $baseRoute = '';


    private $requestedMethod = '';


    private $serverBasePath;


    private $namespace = '';


    /**
     * Undocumented function
     *
     * @param [type] $methods
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public function before($methods, $pattern, \Closure $fn) : void
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->beforeRoutes[$method][] = [
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
    public function match($methods, $pattern, $fn) : void
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;
        foreach (explode('|', $methods) as $method) {
            $this->afterRoutes[$method][] = [
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
    public function all($pattern,  $fn) : void
    {
        $this->match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public function get($pattern, $fn) : void
    {
        $this->match('GET', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public function post($pattern, $fn) :void
    {
        $this->match('POST', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public function patch($pattern, \Closure $fn) :void
    {
        $this->match('PATCH', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public function delete($pattern, $fn) :void
    {
        $this->match('DELETE', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public function put($pattern, $fn) :void
    {
        $this->match('PUT', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param [type] $pattern
     * @param [type] $fn
     * @return void
     */
    public function options($pattern, $fn) :void
    {
        $this->match('OPTIONS', $pattern, $fn);
    }


    /**
     * Undocumented function
     *
     * @param string $baseRoute
     * @param callback $fn
     * @return void
     */
    public function group(string $baseRoute, \Closure $fn)
    {
        $curBaseRoute = $this->baseRoute;
        $this->baseRoute .= $baseRoute;
        call_user_func($fn);
        $this->baseRoute = $curBaseRoute;
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    public function getRequestHeaders()
    {
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
    public function getRequestMethod(): string
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->getRequestHeaders();
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
    public function setNamespace(string $namespace)
    {
        if (is_string($namespace)) {
            $this->namespace = $namespace;
        }
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }


    /**
     * Undocumented function
     *
     * @param [type] $callback
     * @return void
     */
    public function run($callback = null)
    {
        $this->requestedMethod = $this->getRequestMethod();
        if (isset($this->beforeRoutes[$this->requestedMethod])) {
            $this->handle($this->beforeRoutes[$this->requestedMethod]);
        }
        $numHandled = 0;
        if (isset($this->afterRoutes[$this->requestedMethod])) {
            $numHandled = $this->handle($this->afterRoutes[$this->requestedMethod], true);
        }
        if ($numHandled === 0) {
            if ($this->notFoundCallback) {
                $this->invoke($this->notFoundCallback);
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
    public function set404($fn): void
    {
        $this->notFoundCallback = $fn;
    }


    /**
     * Undocumented function
     *
     * @param [type] $routes
     * @param boolean $quitAfterRun
     * @return void
     */
    private function handle($routes, $quitAfterRun = false)
    {
        $numHandled = 0;
        $uri = $this->getCurrentUri();
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
                $this->invoke($route['fn'], $params);
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
    private function invoke($fn, $params = []): void
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {
            list($controller, $method) = explode('@', $fn);
            if ($this->getNamespace() !== '') {
                $controller = $this->getNamespace() . '\\' . $controller;
            }
            if (class_exists($controller)) {
                $additionnalparams = Resolver::resolveFunction($controller, $method);
                $params = array_merge($additionnalparams, $params);
                $function_result = call_user_func_array([Resolver::resolve($controller), $method], $params);
                if ( $function_result === false) {
                    if ($function_result = forward_static_call_array([$controller, $method], $params) === false);
                }
                //var_dump($function_result);
                if($function_result instanceof Response){
                    echo $function_result->finalize();
                }
            }
        }
    }


    /**
     * Undocumented function
     *
     * @return string
     */
    public function getCurrentUri(): string
    {
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->getBasePath()));
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
    public function getBasePath()
    {
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }
        return $this->serverBasePath;
    }


    /**
     * Undocumented function
     *
     * @param [type] $serverBasePath
     * @return void
     */
    public function setBasePath($serverBasePath) : void
    {
        $this->serverBasePath = $serverBasePath;
    }
}
