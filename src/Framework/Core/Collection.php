<?php

namespace Framework\Core;

use ArrayObject;

class Collection extends ArrayObject
{

    protected $array = [];


    public function __construct(array $collection)
    {
        $this->array = $collection;
    }

    public function first()
    {
        return isset($this->array[0]) ? $this->array[0] : null;
    }


    public function get($max = -1)
    {
        if($max != -1){
            return array_slice($this->array,0, $max);
        }
        return $this->array;
    }

    public function last()
    {
        return end($this->array);
    }

    public function with(string ...$args)
    {
        foreach ($args as $arg) {
            foreach ($this->array as $element) {
                $element->{$arg} = call_user_func(array($element, $arg));
            }
        }
        return $this;
    }
}
