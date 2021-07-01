<?php
namespace Framework\Core\Exceptions;

class ModelNotFoundException extends Exception{
    protected $message = "ModelNotFoundException : Model Not Found";
    protected $code = 404;
}