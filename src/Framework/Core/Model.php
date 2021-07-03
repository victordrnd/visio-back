<?php

namespace Framework\Core;

use Framework\ORM\QueryBuilder;

abstract class Model extends QueryBuilder implements \JsonSerializable {
    
    
    protected static $primaryKey = "id";

    private $model_properties = [];

    protected $hidden = [];
    

    public function __get(string $name){
        if(isset($this->model_properties[$name])){
            return $this->model_properties[$name];
        }
        else{
            if(method_exists(get_called_class(), $name)){
                return call_user_func(array($this, $name));
            }
            return NULL;
        }
    }

    public function __set(string $name, $value){
        $this->model_properties[$name] = $value;
    }
    
    public function getPrimaryKeyValue()
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

    public function getProperties() : array{
        return $this->model_properties;
    }

    public function jsonSerialize()
    {
        $cloned_properties = array_diff_key($this->model_properties, array_flip($this->hidden));
        return $cloned_properties;
    }
}
?>