<?php
namespace Framework\ORM\Query;

use Framework\Core\Environment;

class BaseQuery{

    private static $instance = null;


    private function __construct($entity = null)
    {
        $this->entity = $entity;
        $this->table = self::table($entity);
    }
    private static function get_instance($entity){
        if(is_null(static::$instance)){
            static::$instance =  new self($entity);
        }
        return static::$instance;
    }
    
    private $selects = [];
    
    private $wheres = [];
    
    private $ordersBy = [];
    
    private $groupsBy = [];
    
    private $limit = -1;
    private $with = [];

    private $table = "";
    
    private $values_bindings = [];
    private $entity;

    

    public static function select(...$columns){
        $instance = self::get_instance(get_called_class());
        $instance->selects = array_merge($instance->selects, $columns);
        return $instance;  
    }

    public static function where($column, $operator = "=", $value = null){
        $instance = self::get_instance(get_called_class());
        if(is_null($value)){
            $value = $operator;
            $operator = "=";
        }
        $instance->wheres[] = new WhereQuery($column, $operator, $value);
        return $instance;
    }


    public function orWhere($column, $operator = "=", $value = null){
        if(is_null($value)){
            $value = $operator;
            $operator = "=";
        }
        $this->wheres[] = new WhereQuery($column, $operator, $value, WhereQuery::OR);
        return $this;
    }

    public static function limit($count){
        $instance = self::get_instance(get_called_class());
        $instance->limit = $count;
        return $instance;
    }

    public static function orderBy($column, $direction = "ASC"){
        $instance = self::get_instance(get_called_class());
        $order_instance = new OrderByQuery($column, $direction);
        $instance->ordersBy[] = $order_instance;
        return $instance;
    }

    public static function groupBy(array ...$columns){
        $instance = self::get_instance(get_called_class());
        $instance->groupsBy = array_merge($instance->groupsBy, $columns);
        return $instance;
    }

    public function with(...$relationships){
        $this->with = array_merge($this->with, $relationships);
        return $this;
    }
    

    public function first(){
        $this->limit = 1;
        $SQL = $this->build();
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $statement->execute($this->values_bindings);
        $object =  $statement->fetchObject($this->entity);
        static::$instance = null;
        if(!empty($this->with)){
            return call_user_func_array(array($object, "with"), $this->with);
        }
        return $object;
    }

    public function last(){
        $this->ordersBy[] = new OrderByQuery($this->entity::$primaryKey, "DESC");
        return $this->first();
    }


    public function get(){
        $SQL = $this->build();
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $statement->execute($this->values_bindings);
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        static::$instance = null;
        $items =  $statement->fetchAll();
        if(!empty($this->with)){
            foreach($items as $item){
                call_user_func_array(array($item, "with"), $this->with);
            }
        }
        return $items;
    }


    public static function count(){
        $instance = self::get_instance(get_called_class());
        $instance->selects = ["COUNT(*) as count"];
        $SQL = $instance->build();
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $statement->execute($instance->values_bindings);
        $res = $statement->fetchObject();
        return $res->count;
    }

    private function build() : string {
        $SQL = "SELECT ";
        //SELECT 
        if(empty($this->selects)){
            $SQL .= "*";
        }else{
            $SQL .= implode(", ", $this->selects);
        }

        //FROM
        $SQL .= " FROM " . $this->table;

        //WHERE
        if(!empty($this->wheres)){
            foreach($this->wheres as $index => $where_instance){
                if($index == 0){
                    $SQL .= " WHERE " . $where_instance->column ." ". $where_instance->operator." ?";
                }else{
                    if($where_instance->type == WhereQuery::AND){
                        $SQL .= " AND ";
                    }else{
                        $SQL = " OR ";
                    }
                    $SQL .= $where_instance->column ." ". $where_instance->operator." ?";
                }
                $this->values_bindings[] = $where_instance->value;
            }
        }

        //GROUP BY
        if(!empty($this->groupsBy))
            $SQL .= " GROUP BY ".implode(", ", $this->groupsBy);

        //ORDER BY
        if(!empty($this->ordersBy)){
            $orders_by = [];
            foreach($this->ordersBy as $order_item){
                $orders_by[] = strval($order_item); 
            }
            $SQL .= " ORDER BY ".implode(", ", $orders_by);
        }
        
        //LIMIT
        if($this->limit > 0)
            $SQL .= " LIMIT ".$this->limit;

        $SQL.= ";";
        return $SQL;
    }

    protected static function table($entity) {

        $class_path = explode("\\", $entity);
        $table_name = strtolower(array_pop($class_path));
        return $table_name;
    }


}

?>