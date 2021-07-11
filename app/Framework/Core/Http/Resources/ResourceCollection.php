<?php

namespace Framework\Core\Http\Resources;

use Framework\Core\App;
use Framework\Core\Collection;
use Framework\Core\Contracts\Resources\JsonResourceInterface;

abstract class ResourceCollection implements JsonResourceInterface, \JsonSerializable{

    protected $collection  = null;

    public function __construct(Collection $collection){
        $this->collection = $collection;
    }
   


    public function jsonSerialize() {
        return $this->toArray(App::request());
    }
}