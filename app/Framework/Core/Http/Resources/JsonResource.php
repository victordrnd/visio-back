<?php


namespace Framework\Core\Http\Resources;

use Framework\Core\App;
use Framework\Core\Contracts\Resources\JsonResourceInterface;
use Framework\Core\Model;

abstract class JsonResource implements JsonResourceInterface, \JsonSerializable{

    private $model= null;

    public function __construct(Model $model){
        $this->model = $model;
    }
   

    public function __get($key){
        return $this->model->{$key};
    }


    public function __call(string $function, array $arguments){
        if(method_exists($this->model, $function)){
            return call_user_func_array(array($this->model, $function), $arguments);
        }
    }



    public function jsonSerialize() {
        return $this->toArray(App::request());
    }
}