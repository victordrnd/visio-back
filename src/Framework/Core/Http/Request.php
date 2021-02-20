<?php

namespace Framework\Core\Http;

class Request
{

    public $url;

    public $params = [];


    public function __construct()
    {
        $this->extractInputs();
        $this->extractHeader();
    }


    public function all()
    {
        return $this->params;
    }

    public function only(string ...$params)
    {
        $data = [];
        foreach ($params as $param) {
            if ($this->has($param)) {
                $data[] = $this->{$param};
            }
        }
        return $data;
    }


    public function input(string $input)
    {
        return $this->{$input};
    }


    public function has(string $input)
    {
        return isset($this->{$input});
    }


    private function extractInputs()
    {
        $data = file_get_contents("php://input");
        parse_str($data, $this->params);
        foreach ($this->params as $param => &$value) {
            if (!is_array($this->params)) {
                $value = htmlspecialchars($value);
            }
            $this->{$param} = $value;
        }
    }

    private function extractHeader()
    {
        $this->url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


    public function __get($property)
    {
        return null;
    }
}


