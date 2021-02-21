<?php
namespace Framework\Core\Exceptions;

class ModelNotFoundException extends \Exception{
    protected $message = "Model Not Found";
    protected $code = 404;
}