<?php

namespace Framework\Core;

use ArrayIterator;
use ArrayObject;
use Iterator;

class Collection extends ArrayObject implements \JsonSerializable, \IteratorAggregate{

    protected $array = [];
    private $index = 0;

    public function __construct(array $collection) {
        $this->array = $collection;
    }

    public function toArray(){
        return $this->array;
    }

    public function pluck($field){
        return $this->map(function($el) use ($field){
            return $el->{$field};
        });
    }

    public function first() {
        return isset($this->array[0]) ? $this->array[0] : null;
    }


    public function get($max = -1) {
        if ($max != -1) {
            return array_slice($this->array, 0, $max);
        }
        return $this->array;
    }

    public function last() {
        return end($this->array);
    }

    public function with(string ...$args) {
        foreach ($args as $arg) {
            foreach ($this->array as &$element) {
                $element->{$arg} = call_user_func_array(array($element, "load"), $args);
            }
        }
        return $this;
    }


    public function map($callback) : array {
        $collection = [];
        foreach($this->array as $index=>$element){
           $collection[] = $callback($element, $index); 
        }
        return $collection;
    }


    public function getIterator() {
        return new ArrayIterator($this->array);
    }


    public function jsonSerialize(){
        return $this->array;
    }
}
