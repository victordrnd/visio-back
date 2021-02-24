<?php
namespace Framework\Core\Exceptions;

use Throwable;

class UnknowValidationRuleException extends \Exception{
    protected $message = "Unknow validation rule";
    protected $code = 401;


    public function __construct(string $message = "Unknow validation rule",int $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code,$previous);
    }
}