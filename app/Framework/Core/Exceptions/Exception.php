<?php
namespace Framework\Core\Exceptions;

class Exception extends \Exception implements \JsonSerializable{
    protected $message = "An error has occured";
    protected $code = 401;


    public function jsonSerialize() {
        return get_class() ." : {$this->message}";
    }
}