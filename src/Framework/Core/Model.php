<?php

namespace Framework\Core;

use Framework\ORM\QueryBuilder;

abstract class Model extends QueryBuilder{

    protected static $table = "";
    
    protected static $primaryKey = "id";

    protected $model_properties = [];


    protected static function table(){
        if(!static::$table){
            $class_path = explode("\\", get_called_class());
            static::$table = strtolower(array_pop($class_path));
        }
        return static::$table;
    }


    public function __get(string $name){
        if(isset($this->model_properties[$name]))
            return $this->model_properties[$name];
        return NULL;
    }

    public function __set(string $name, $value){
        $this->model_properties[$name] = $value;
    }
    
    protected function getPrimaryKeyValue()
    {
        return $this->{static::$primaryKey};
    }

    protected function setPrimaryKeyValue(int $value)
    {
        $class = new \ReflectionClass(get_called_class());
        if($class->hasProperty("primaryKey")){
            $this->{static::$primaryKey} = $value;
        }
    }


    public function __toString()
    {
        return json_encode($this->model_properties);
    }
}
?>