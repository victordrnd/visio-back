<?php

namespace Framework\ORM\Query;

class OrderByQuery{

    public $column = "";
    public $direction = "ASC";

    public function __construct($column, $direction = "ASC"){
        $this->column = $column;
        if(in_array($direction, ["ASC", "DESC", 'asc', 'desc']))
            $this->direction = $direction;
    }

    public function __toString()
    {
        return $this->column." ".$this->direction;
    }


}