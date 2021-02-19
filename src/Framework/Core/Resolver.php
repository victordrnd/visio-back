<?php

namespace Framework\Core;

/**
 * used in the Router class to automatically inject the dependencies to Controller
 */
class Resolver
{

    /**
     * Build automatically the given class
     *
     * @param [type] $class
     * @return void
     */
    public static function resolve($class)
    {

        $reflect = new \ReflectionClass($class);

        $construct = $reflect->getConstructor();
        if (is_null($construct)) {
            return new $class;
        }
        $params = $construct->getParameters();
        $dependencies = self::getDependencies($params);
        return $reflect->newInstanceArgs($dependencies);
    }

    /**
     * Return list of dependencies of a function
     *
     * @param [type] $class
     * @param [type] $method
     * @return array
     */
    public static function resolveFunction($class, $method)
    {
        $reflect = new \ReflectionMethod($class, $method);
        $parameters = $reflect->getParameters();
        return self::getDependencies($parameters);
    }

    /**
     * Build dependencies from the params given 
     *
     * @param [type] $params
     * @return void
     */
    public static function getDependencies($params)
    {
        $dependencies = [];
        foreach ($params as $param) {
            $paramClass = $param->getClass();
            if (is_null($paramClass)) {
                if ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                }
            } else {
                $dependencies[] = self::resolve($paramClass->name);
            }
        }
        return $dependencies;
    }
}
