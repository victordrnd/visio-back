<?php

namespace Framework\Core;

use Framework\ORM\QueryBuilder;

abstract class Model implements \JsonSerializable{

    use QueryBuilder;
    
    protected static $table = "";
    
    protected static $primaryKey = "id";

    protected $model_properties = [];

    protected $hidden = [];
    

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


    public function jsonSerialize()
    {
        $cloned_properties = array_diff_key($this->model_properties, array_flip($this->hidden));
        return $cloned_properties;
    }
}
?>