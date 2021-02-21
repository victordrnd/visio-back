<?php
namespace Framework\Core\Exceptions;

class InvalidTokenException extends \Exception{
    protected $message = "Invalid Token";
    protected $code = 401;
}