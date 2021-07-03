<?php

namespace Framework\Core\Http;

use Framework\Core\Exceptions\UnknowValidationRule;
use Framework\Core\Exceptions\UnknowValidationRuleException;

class Request {

    public $url;

    public $params = [];
    public $headers;

    public function __construct() {
        $this->extractInputs();
        $this->extractHeader();
        
    }


    public function all() {
        return $this->params;
    }

    public function only(string ...$params) {
        $data = [];
        foreach ($params as $param) {
            if ($this->has($param)) {
                $data[] = $this->{$param};
            }
        }
        return $data;
    }


    public function input(string $input) {
        return $this->{$input};
    }


    public function has(string $input) {
        return isset($this->{$input});
    }


    private function extractInputs() {
        if (isset($_SERVER['QUERY_STRING'])) {
            $data = $_SERVER['QUERY_STRING'];
            parse_str($data, $this->params);
            foreach ($this->params as $param => &$value) {
                if (!is_array($this->params)) {
                    $value = htmlspecialchars($value);
                }
                $this->{$param} = $value;
            }
        }
    }

    private function extractHeader() {
        $this->url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->headers = HttpHeader::getHeaders();
    }


    public function __get($property) {
        return $this->params[$property];
    }

    public function rules(){
        return [];
    }


    public function validate(){
        $rules = $this->rules();
        $errors = [];
        foreach($rules as $input => $rule_array){
            foreach($rule_array as $rule){
                switch (trim($rule)) {
                    case 'required':
                        if (!isset($this->{$input}))
                            $errors[$input][] = "field $input is required";
                    break;
                    case "string":
                        if(isset($this->{$input})){
                            if(!is_string($this->{$input}))
                                $errors[$input][] = "field $input must be a string";
                        }
                    break;
                    case "integer" :
                        if(isset($this->{$input})){
                            if(!ctype_digit($this->{$input}))
                                $errors[$input][] = "field $input must be an integer";
                        }
                    break;
                    case "numeric" :
                        if(isset($this->{$input})){
                            if(!is_numeric($this->{$input}))
                                $errors[$input][] = "field $input must be a numeric value";
                        }
                    break;
                    case "array":
                        if(isset($this->{$input})){
                            if(!is_array($this->{$input}))
                                $errors[$input][] = "field $input must be an array";
                        }
                    break;
                    default:
                        throw new UnknowValidationRuleException("Unknow rule $rule",401, null);
                        return false; 
                }
            }    
        }
        if(count($errors) > 0)
            return $errors;
        return true;
    }
}
