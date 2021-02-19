<?php
namespace Framework\Core;

class Response {

    private $status_code  = 200;
    private $content = null;
    private $headers = [];
    
    public function json($content, $code = 200, $headers = []){
        if(!is_array($content)){
            $this->content = strval($content);
        }else{
            $this->content = json_encode($content);
        }
        $this->status_code = $code;
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public  function finalize()
    {
        foreach($this->headers as $header){
            header($header);
        }
        http_response_code($this->status_code);
        return $this->content;
    }
}

?>