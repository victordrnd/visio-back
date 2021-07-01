<?php
namespace Framework\Core\Exceptions;

class RelationNotFoundException extends Exception{
    protected $message = "RelationNotFoundException : call to undefined relationship";
    protected $code = 404;

    public function __construct($relationship, $model){
        $this->message = "Call to undefined relationship [$relationship] on model [$model]";
    }
}