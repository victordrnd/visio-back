<?php

namespace Framework\ORM\Query;

class WhereQuery{

    const OPERATORS = ['=', '>=', '>', '<', '<=', '!=', 'LIKE', 'NOT LIKE'];

    public $column = "";

    public $operator = "=";

    public $value = null;

    const AND = 0; 
    const OR = 1;

    public $type = self::AND;

    public function __construct($column, $operator = "=", $value = null, $type = self::AND)
    {
        $this->column = $column;
        if (in_array($operator, self::OPERATORS)) {
            $this->operator = $operator;
        }else{
            $this->operator = "=";
        }
        $this->value = $value;
        $this->type = $type;
    }
}