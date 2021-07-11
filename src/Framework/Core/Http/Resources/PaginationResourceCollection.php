<?php


namespace Framework\Core\Http\Resources;
use Framework\Core\Contracts\Resources\JsonResourceInterface;
use Framework\Core\Http\Request;

class PaginationResourceCollection implements \JsonSerializable{

    private $items= null;
    private $per_page = 15;
    private $total;
    public function __construct(array $items, int $per_page, $total){
        $this->items = $items;
        $this->per_page = $per_page;
        $this->total = $total;
    }
   

    public function __get($key){
        return $this->model->{$key};
    }


    public function __call(string $function, array $arguments){
        return call_user_func_array(array($this->model, $function), $arguments);
    }


    public function jsonSerialize() {
        $page = intval(request()->page);
        $from = (($page -1) * $this->per_page) + 1 ;
        $to = count($this->items) < $this->per_page ? $from + count($this->items) : $from  + $this->per_page - 1;
        return [
            'per_page' => $this->per_page,
            'page' =>  $page ? intval($page) : 1,
            'path' => request()->uri,
            'next_page_url' => ($page >= 1 && $to != $this->total) ? request()->uri."?page=".($page+1) : null,
            'prev_page_url' => $page > 1 ? request()->uri."?page=".($page-1) : null ,
            'total' => $this->total,
            'from' => $from,
            'to' => count($this->items) ?  $to : null,
            'data' => $this->toArray(),
        ];
    }

    public function toArray()
    {
        return $this->items;
    }
}