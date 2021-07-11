<?php

class Autoloader
{

    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public static function autoload($class)
    {
        if($class == App::class){
            require_once dirname(__FILE__).DIRECTORY_SEPARATOR."Framework".DIRECTORY_SEPARATOR."Core".DIRECTORY_SEPARATOR."App.php";
            return;
        }
        $parts = preg_split('#\\\#', $class);
        $className = array_pop($parts);


        $path = implode(DIRECTORY_SEPARATOR, $parts);
        $file = $className . '.php';

        $filepath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file;
        require_once $filepath;
    }
}
