<?php

namespace Framework\ORM\Query;

use Framework\Core\Collection;
use Framework\Core\Environment;
use Framework\Core\Exceptions\ModelNotFoundException;

class BaseQuery {

    private static $instance = null;


    private function __construct($entity = null) {
        $this->entity = $entity;
        $this->table = self::table($entity);
    }
    private static function get_instance($entity) {
        if (is_null(static::$instance)) {
            static::$instance =  new self($entity);
        }
        return static::$instance;
    }
    private $type = QueryType::SELECT;

    private $selects = [];

    private $wheres = [];

    private $ordersBy = [];

    private $groupsBy = [];


    private $limit = -1;
    private $offset = -1;

    private $with = [];

    //CREATE OR UPDATE inputs ['label' => 'test']
    private $inputs = [];

    private $table = "";

    private $values_bindings = [];
    private $entity;


    public $SQL;

    public static function select(...$columns) {
        $instance = self::get_instance(get_called_class());
        $instance->selects = array_merge($instance->selects, $columns);
        return $instance;
    }


    public function update(array $inputs) {
        $instance = self::get_instance(get_called_class());
        $instance->type = QueryType::UPDATE;
        $instance->inputs = $inputs;
        $instance->build();
        $statement = Environment::getInstance()->cnx->prepare($instance->SQL);
        $statement->execute($instance->values_bindings);
        $instance->values_bindings = [];
        $static = !(isset($this) && get_class($this) == __CLASS__);
        if($static){
            return $instance->get();
        }else{
            return $instance->first();
        }
    }


    public function delete(){
        $instance = self::get_instance(get_called_class());
        $instance->type = QueryType::DELETE;
        $instance->build();
        $statement = Environment::getInstance()->cnx->prepare($instance->SQL);
        return $statement->execute($instance->values_bindings);
    }

    public static function where($column, $operator = "=", $value = null) {
        $instance = self::get_instance(get_called_class());
        if (is_null($value)) {
            $value = $operator;
            $operator = "=";
        }
        $instance->wheres[] = new WhereQuery($column, $operator, $value);
        return $instance;
    }


    public function orWhere($column, $operator = "=", $value = null) {
        if (is_null($value)) {
            $value = $operator;
            $operator = "=";
        }
        $this->wheres[] = new WhereQuery($column, $operator, $value, WhereQuery::OR);
        return $this;
    }

    public static function limit(int $count) {
        $instance = self::get_instance(get_called_class());
        $instance->limit = $count;
        return $instance;
    }

    public static function offset(int $offset) {
        $instance = self::get_instance(get_called_class());
        $instance->offset = $offset;
        return $instance;
    }

    public static function orderBy($column, $direction = "ASC") {
        $instance = self::get_instance(get_called_class());
        $order_instance = new OrderByQuery($column, $direction);
        $instance->ordersBy[] = $order_instance;
        return $instance;
    }

    public static function groupBy(array ...$columns) {
        $instance = self::get_instance(get_called_class());
        $instance->groupsBy = array_merge($instance->groupsBy, $columns);
        return $instance;
    }

    public function with(...$relationships) {
        $this->with = array_merge($this->with, $relationships);
        return $this;
    }


    public function first() {
        $this->limit = 1;
        $this->type = QueryType::SELECT;
        $this->build();
        $statement = Environment::getInstance()->cnx->prepare($this->SQL);
        $statement->execute($this->values_bindings);
        $object =  $statement->fetchObject($this->entity);
        static::$instance = null;
        if (is_null($object) || is_bool($object)) {
            throw new ModelNotFoundException;
        }
        if (!empty($this->with)) {
            return call_user_func_array(array($object, "with"), $this->with);
        }
        return $object;
    }

    public function last() {
        $this->ordersBy[] = new OrderByQuery($this->entity::$primaryKey, "DESC");
        return $this->first();
    }


    public function get() {
        $this->type = QueryType::SELECT;
        $this->build();
        $statement = Environment::getInstance()->cnx->prepare($this->SQL);
        $statement->execute($this->values_bindings);
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        static::$instance = null;
        $items =  $statement->fetchAll();
        if (!empty($this->with)) {
            foreach ($items as $item) {
                call_user_func_array(array($item, "with"), $this->with);
            }
        }
        return new Collection($items);
    }


    public function paginate(int $per_page = 15, int $page = 1) {
        $this->limit = $per_page;
        $this->offset = ($page - 1) * $per_page;
        return $this->get();
    }

    public static function count() {
        $instance = self::get_instance(get_called_class());
        $instance->selects = ["COUNT(*) as count"];
        $instance->build();
        $statement = Environment::getInstance()->cnx->prepare($instance->SQL);
        $statement->execute($instance->values_bindings);
        $res = $statement->fetchObject();
        return $res->count;
    }

    private function build(): void {
        switch ($this->type) {
            case QueryType::SELECT:
                $this->buildSelect();
                break;
            case QueryType::UPDATE:
                $this->buildUpdate();
                break;
            case QueryType::DELETE:
                $this->buildDelete();
                break;
        }
        $this->SQL .= ";";
    }



    protected static function table($entity) {
        if (is_string($entity) || is_object($entity)) {
            if (isset($entity::$table))
                return $entity::$table;
        }
        $class_path = explode("\\", $entity);
        $table_name = strtolower(array_pop($class_path));
        return $table_name;
    }


    private function buildSelect(): void {
        $this->SQL = "SELECT ";
        //SELECT 
        if (empty($this->selects)) {
            $this->SQL .= "*";
        } else {
            $this->SQL .= implode(", ", $this->selects);
        }
        //FROM
        $this->SQL .= $this->buildFrom();

        //WHERE
        $this->SQL .= $this->buildWhere();

        //GROUP BY
        $this->SQL .= $this->buildGroupBy();

        //ORDER BY
        $this->buildOrderBy();

        //LIMIT & OFFSET
        $this->buildLimitOffset();
    }


    private function buildUpdate() {
        $this->SQL = "UPDATE " . $this->table;
        $this->buildUpdateSet();
        $this->buildWhere();
    }

    private function buildDelete(){
        $this->SQL = "DELETE ";
        $this->buildFrom();
        $this->buildWhere();
    }

    private function buildFrom() {
        $this->SQL .= " FROM " . $this->table;
    }

    private function buildWhere(): void {
        if (!empty($this->wheres)) {
            foreach ($this->wheres as $index => $where_instance) {
                if ($index == 0) {
                    $this->SQL .= " WHERE " . $where_instance->column . " " . $where_instance->operator . " ?";
                } else {
                    if ($where_instance->type == WhereQuery::AND) {
                        $this->SQL .= " AND ";
                    } else {
                        $this->SQL = " OR ";
                    }
                    $this->SQL .= $where_instance->column . " " . $where_instance->operator . " ?";
                }
                $this->values_bindings[] = $where_instance->value;
            }
        }
    }


    private function buildGroupBy() {
        if (!empty($this->groupsBy))
            $this->SQL .= " GROUP BY " . implode(", ", $this->groupsBy);
    }

    private function buildOrderBy() {
        if (!empty($this->ordersBy)) {

            $orders_by = [];
            foreach ($this->ordersBy as $order_item) {
                $orders_by[] = strval($order_item);
            }
            $this->SQL .= " ORDER BY " . implode(", ", $orders_by);
        }
    }


    private function buildLimitOffset() {
        if ($this->limit > 0)
            $this->SQL .= " LIMIT " . $this->limit;

        if ($this->offset > 0)
            $this->SQL .= " OFFSET " . $this->offset;
    }


    private function buildUpdateSet() {
        $this->SQL .= " SET";
        $end_key = array_keys($this->inputs);
        $last_key = end($end_key);
        foreach ($this->inputs as $key => $value) {
            if ($key == $last_key) {
                $this->SQL .= " $key = ?";
            } else {
                $this->SQL .= " $key = ?,";
            }
            $this->values_bindings[] = $value;
        }
    }
}
