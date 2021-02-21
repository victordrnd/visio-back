<?php
namespace Framework\Core\Exceptions;

class InvalidCredentialsException extends \Exception{
    protected $message = "Invalid Credentials";
    protected $code = 401;
}