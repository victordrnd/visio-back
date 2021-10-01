<?php

namespace Framework\ORM\Query;

use Framework\Core\App;
use Framework\Core\Collection;
use Framework\Core\Environment;
use Framework\Core\Exceptions\ModelNotFoundException;
use Framework\Core\Http\Resources\PaginationResourceCollection;
use Framework\ORM\Relationships\MultipleRelationship;
use Framework\ORM\Relationships\UniqueRelationship;
use Models\Room;

class BaseQuery
{

    private static $instance = null;

    public function __construct($entity = null)
    {
        if (is_null($entity)) {
            $this->entity = get_called_class();
        } else {
            $this->entity = $entity;
        }
        $this->model = $this;
        $this->table = self::table($entity);
    }



    private static function get_instance($entity)
    {
        if (is_null(static::$instance)) {
            static::$instance =  new self($entity);
        }
        return static::$instance;
    }
    private $type = QueryType::SELECT;

    private $selects = [];

    private $wheres = [];

    private $wherehas = [];

    private $ordersBy = [];

    private $groupsBy = [];


    private $limit = -1;
    private $offset = -1;

    private $with = [];

    //CREATE OR UPDATE inputs ['label' => 'test']
    private $inputs = [];

    private $table = "";

    private $values_bindings = [];
    public $entity;
    private $model;


    private $SQL;

    public static function select(...$columns)
    {
        $instance = self::get_instance(get_called_class());
        $instance->selects = array_merge($instance->selects, $columns);
        return $instance;
    }


    public static function create(array $inputs)
    {
        $instance = self::get_instance(get_called_class());
        $instance->type = QueryType::INSERT;
        $instance->inputs = $inputs;
        $instance->touchModel(true);
        $instance->build();
        var_dump($instance->SQL, $instance->values_bindings);
        $cnx = Environment::getInstance()->cnx;
        $cnx->setAttribute(\PDO::ATTR_EMULATE_PREPARES, TRUE);
        $statement = $cnx->prepare($instance->SQL);
        $statement->execute($instance->values_bindings);
        $instance->values_bindings = [];
        return $instance->getLastInsertedInstance();
    }


    public function update(array $wheres, array $inputs2 = [])
    {
        $instance = self::get_instance(get_called_class());
        if (empty($inputs2)) {
            $inputs = $wheres;
        } else {
            $inputs = $inputs2;
            foreach ($wheres as $column => $value) {
                $instance->where($column, $value);
            }
        }
        $instance->type = QueryType::UPDATE;
        $instance->inputs = $inputs;
        $instance->touchModel();
        $instance->build();
        $statement = Environment::getInstance()->cnx->prepare($instance->SQL);
        $statement->execute($instance->values_bindings);
        $instance->values_bindings = [];
        $static = !(isset($this) && get_class($this) == __CLASS__);
        if ($static)
            return $instance->get();
        return $instance->first();
    }



    public function delete()
    {
        $instance = self::get_instance(get_called_class());
        $instance->type = QueryType::DELETE;
        $instance->build();
        $statement = Environment::getInstance()->cnx->prepare($instance->SQL);
        return $statement->execute($instance->values_bindings);
    }

    public static function where($column, $operator = "=", $value = null)
    {
        $instance = self::get_instance(get_called_class());
        if (is_null($value)) {
            $value = $operator;
            $operator = "=";
        }
        $instance->wheres[] = new WhereQuery($column, $operator, $value);
        return $instance;
    }


    public static function whereHas($relation, callable $callback = null)
    {
        $instance = self::get_instance(get_called_class());
        $instance->wherehas[$relation] = $callback;
        return $instance;
    }


    public function orWhere($column, $operator = "=", $value = null)
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = "=";
        }
        $this->wheres[] = new WhereQuery($column, $operator, $value, WhereQuery::OR);
        return $this;
    }

    public static function whereNull($column)
    {
        $instance = self::get_instance(get_called_class());
        $instance->wheres[] = new WhereQuery($column, 'IS NULL', null);
        return $instance;
    }

    public function orWhereNull($column)
    {
        $this->wheres[] = new WhereQuery($column, 'IS NULL', null, WhereQuery::OR);
        return $this;
    }

    public static function whereIn($column, $values){
        $instance = self::get_instance(get_called_class());
        $instance->wheres[] = new WhereQuery($column, 'IN', $values);
        return $instance;
    }

    public function orWhereIn($column, $values)
    {
        $this->wheres[] = new WhereQuery($column, 'IN', $values, WhereQuery::OR);
        return $this;
    }

    public static function whereNotIn($column, $values){
        $instance = self::get_instance(get_called_class());
        $instance->wheres[] = new WhereQuery($column, 'NOT IN', $values);
        return $instance;
    }

    public function orWhereNotIn($column, $values)
    {
        $this->wheres[] = new WhereQuery($column, 'NOT IN', $values, WhereQuery::OR);
        return $this;
    }

    public static function whereNotNull($column)
    {
        $instance = self::get_instance(get_called_class());
        $instance->wheres[] = new WhereQuery($column, 'IS NOT NULL');
        return $instance;
    }

    public function orWhereNotNull($column)
    {
        $this->wheres[] = new WhereQuery($column, 'IS NOT NULL', null, WhereQuery::OR);
        return $this;
    }

    public static function limit(int $count)
    {
        $instance = self::get_instance(get_called_class());
        $instance->limit = $count;
        return $instance;
    }

    public static function offset(int $offset)
    {
        $instance = self::get_instance(get_called_class());
        $instance->offset = $offset;
        return $instance;
    }

    public static function orderBy($column, $direction = "ASC")
    {
        $instance = self::get_instance(get_called_class());
        $order_instance = new OrderByQuery($column, $direction);
        $instance->ordersBy[] = $order_instance;
        return $instance;
    }

    public static function groupBy(array ...$columns)
    {
        $instance = self::get_instance(get_called_class());
        $instance->groupsBy = array_merge($instance->groupsBy, $columns);
        return $instance;
    }

    public function with(...$relationships)
    {
        $instance = self::get_instance(get_called_class());
        $instance->with = array_merge($instance->with, $relationships);
        return $instance;
    }


    public function first()
    {
        $this->limit = 1;
        $this->type = QueryType::SELECT;
        $this->build();
        $statement = Environment::getInstance()->cnx->prepare($this->SQL);
        $statement->execute($this->values_bindings);
        $object =  $statement->fetchObject($this->entity);
        static::$instance = null;
        if (is_null($object) || is_bool($object)) {
            return NULL;
            throw new ModelNotFoundException;
        }
        if (!empty($this->with)) {
            return call_user_func_array(array($object, "load"), $this->with);
        }
        return $object;
    }

    public function exists()
    {
        $obj = $this->first();
        if (is_null($obj) || is_bool($obj)) {
            return false;
        }
        return true;
    }

    public function firstOrFail()
    {
        $object = $this->first();
        if (is_null($object) || is_bool($object)) {
            throw new ModelNotFoundException;
        }
        return $object;
    }


    public function last()
    {
        $this->ordersBy[] = new OrderByQuery($this->entity::$primaryKey, "DESC");
        return $this->first();
    }


    public function get($paginate = false, int $total = 0)
    {
        $this->type = QueryType::SELECT;
        $this->build();
        $statement = Environment::getInstance()->cnx->prepare($this->SQL);
        $statement->execute($this->values_bindings);
        if(!count($this->selects)){
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        static::$instance = null;
        $items =  $statement->fetchAll();
        if (!empty($this->with)) {
            foreach ($items as $item) {
                call_user_func_array(array($item, "load"), $this->with);
            }
        }
        if ($paginate)
            return new PaginationResourceCollection($items, $this->limit, $total);
        return new Collection($items);
    }


    public function paginate(int $per_page = 15)
    {
        $page = App::request()->page ?? 1;
        $this->limit = $per_page;
        $this->offset = ($page - 1) * $per_page;
        $total = $this->count();
        $instance = self::get_instance(get_called_class());
        $instance->selects = [];
        $instance->values_bindings = [];
        return $this->get(true, $total);
    }

    public static function count()
    {
        $instance = self::get_instance(get_called_class());
        $instance->selects = ["COUNT(*) as count"];
        $instance->build(false);
        $statement = Environment::getInstance()->cnx->prepare($instance->SQL);
        $statement->execute($instance->values_bindings);
        $res = $statement->fetchObject();
        return $res->count;
    }

    private function build($limit_offset = true): void
    {
        switch ($this->type) {
            case QueryType::SELECT:
                $this->buildSelect($limit_offset);
                break;
            case QueryType::INSERT:
                $this->buildInsert();
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



    protected static function table($entity)
    {
        if (is_string($entity) || is_object($entity)) {
            if (isset($entity::$table))
                return $entity::$table;
        }
        $class_path = explode("\\", $entity);
        $table_name = strtolower(array_pop($class_path));
        return $table_name;
    }


    private function buildSelect($limit_offset = true): void
    {
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

        $this->SQL .= $this->buildWhereHas();

        //GROUP BY
        $this->SQL .= $this->buildGroupBy();

        //ORDER BY
        $this->buildOrderBy();

        //LIMIT & OFFSET
        if ($limit_offset)
            $this->buildLimitOffset();
    }

    private function buildInsert()
    {
        $this->SQL = "INSERT INTO " . $this->table;
        $this->buildInsertValues();
    }

    private function buildUpdate()
    {
        $this->SQL = "UPDATE " . $this->table;
        $this->buildUpdateSet();
        $this->buildWhere();
    }

    private function buildDelete()
    {
        $this->SQL = "DELETE ";
        $this->buildFrom();
        $this->buildWhere();
    }

    private function buildFrom()
    {
        $this->SQL .= " FROM " . $this->table;
    }

    private function buildWhere(): void
    {
        if (!empty($this->wheres)) {
            foreach ($this->wheres as $index => $where_instance) {
                if ($index == 0) {
                    $this->SQL .= " WHERE ";
                } else if ($where_instance->type == WhereQuery::AND) {
                    $this->SQL .= " AND ";
                } else {
                    $this->SQL = " OR ";
                }
                if (in_array($where_instance->operator, ["IS NULL", "IS NOT NULL"])) {
                    $this->SQL .= $where_instance->column . " " . $where_instance->operator;
                } else {
                    if(is_array($where_instance->value)){
                        $this->SQL .= $where_instance->column . " ". $where_instance->operator."( " ;
                        foreach($where_instance->value as $index => $value){
                            if($index == count($where_instance->value)-1){
                                $this->SQL .= " ? )";
                            }else{
                                $this->SQL .= " ?,";
                            }
                            $this->values_bindings[] = $value;
                        }
                    }else{
                        $this->SQL .= $where_instance->column . " " . $where_instance->operator . " ?";
                        $this->values_bindings[] = $where_instance->value;
                    }
                }
            }
        }
    }


    private function buildWhereHas(): void
    {
        if (!empty($this->wherehas)) {
            foreach ($this->wherehas as $relation => $callback) {
                if (!strpos($this->SQL, 'WHERE')) {
                    $this->SQL .= " WHERE ";
                } else {
                    $this->SQL .= " AND ";
                }
                $this->SQL .= " EXISTS (";
                $instance = self::get_instance(get_called_class());
                $class = $instance->entity;
                $relationship = call_user_func_array(array(new $class(), $relation), []);//(array($instance->entity, $relation));
                $query = $callback($relationship->query);
                $this->SQL .= $query->getRawQuery();
                $this->SQL .= ")";
            }
        }
    }


    private function buildGroupBy()
    {
        if (!empty($this->groupsBy))
            $this->SQL .= " GROUP BY " . implode(", ", $this->groupsBy);
    }

    private function buildOrderBy()
    {
        if (!empty($this->ordersBy)) {

            $orders_by = [];
            foreach ($this->ordersBy as $order_item) {
                $orders_by[] = strval($order_item);
            }
            $this->SQL .= " ORDER BY " . implode(", ", $orders_by);
        }
    }


    private function buildLimitOffset()
    {
        if ($this->limit > 0)
            $this->SQL .= " LIMIT " . $this->limit;

        if ($this->offset > 0)
            $this->SQL .= " OFFSET " . $this->offset;
    }

    private function buildInsertValues()
    {
        $this->SQL .= " (";
        $arr_keys = array_keys($this->inputs);
        $last_key = end($arr_keys);
        $indexed = "(";
        foreach ($this->inputs as $key => $column) {
            if ($key != $last_key) {
                $this->SQL .= "$key ,";
                $indexed .= "?,";
            } else {
                $this->SQL .= "$key ) VALUES ";
                $indexed .= "?)";
            }
        }
        $this->SQL .= $indexed;
        foreach (array_values($this->inputs) as &$value) {
            $this->values_bindings[] = htmlspecialchars($value);
        }
    }

    private function buildUpdateSet()
    {
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


    public function getRawQuery()
    {
        $this->build();
        return $this->SQL;
    }


    private function touchModel($created_at = false)
    {
        if ($this->entity::$timestamps) {
            $date = date("Y-m-d H:i:s");
            if ($created_at) {
                $this->inputs = array_merge($this->inputs, ['updated_at' => $date, 'created_at' => $date]);
            } else {
                $this->inputs = array_merge($this->inputs, ['updated_at' => $date]);
            }
        }
    }

    protected function getLastInsertedInstance()
    {
        return $this->entity::orderBy($this->entity::$primaryKey, 'DESC')->limit(1)->first();
    }

    protected function getLastInsertedId()
    {
        $obj = $this->entity::orderBy($this->entity::$primaryKey, 'DESC')->limit(1)->first();
        return is_null($obj) ? null : $obj->{$this->entity::$primaryKey};
    }
}
