<?php
namespace Framework\Core\Exceptions;

class ExpiredTokenException extends \Exception{
    protected $message = "Token has expired";
    protected $code = 401;
}