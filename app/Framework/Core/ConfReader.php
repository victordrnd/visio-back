<?php

namespace Framework\Core;


abstract class ConfReader {

    /**
     *
     * @var [array]
     */
    private static $config;

    
    protected function __construct() {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/config.ini")) {
            self::$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/config.ini');
        } else {

            throw new \Exception("Le fichier de configuration n'a pas pu être lu");
        }
    }

    /**
     *
     * @param string $value
     * @return mixed
     */
    public function getConfigValue(string $value) {
        if (isset(self::$config[$value])) {
            return self::$config[$value];
        } else {
            throw new \Exception("La clé de configuration demandé n'existe pas", 1);
        }
    }


    public static function setConfigValue(string $key, string $value) {
        $content = "";
        $filepath = $_SERVER['DOCUMENT_ROOT'] . "/config.ini";

        $content .= "\n".$key . "=" . $value ;
        if (!$handle = fopen($filepath, 'a')) {
            return false;
        }
        $success = fwrite($handle, $content);
        fclose($handle);
        if($success){
            self::$config[$key] = $value;
        }
        return $success;
    }
}
