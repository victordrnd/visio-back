<?php

namespace Framework\Core;

use Framework\ORM\QueryBuilder;

abstract class Model extends QueryBuilder implements \JsonSerializable{

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
        $cloned_properties = (array)clone(object)$this->model_properties;
        foreach($this->hidden as $hidden_key){
            if(isset($cloned_properties[$hidden_key]))
                unset($cloned_properties[$hidden_key]);
        }
        return $cloned_properties;
    }
}
?>