<?php
namespace Framework\Core;

class Singleton extends ConfReader
{

    public $cnx;
    private static $instance;
    private $host;
    private $dbname;
    private $username;
    private $password;

    private function __construct()
    {
        parent::__construct();
        $this->dbname = $this->getConfigValue("DB_NAME");
        $this->host = $this->getConfigValue("DB_HOST");
        $this->username = $this->getConfigValue("DB_USER");
        $this->password = $this->getConfigValue("DB_PASSWORD");
        $this->cnx = new \PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
    }

    public static function getInstance(): Singleton
    {
        if (is_null(self::$instance)) {
            self::$instance = new Singleton();
        }
        return self::$instance;
    }
}
