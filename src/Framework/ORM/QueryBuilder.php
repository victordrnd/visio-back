<?php

namespace Framework\ORM;

use Framework\Core\Environment;
use Framework\ORM\Relationship;
use Framework\Core\Collection;
use Framework\ORM\Query\BaseQuery;
class QueryBuilder extends BaseQuery{
    use Relationship;

    /**
     * Find entity with selected id
     *
     * @param int $id
     */
    public static function find(int $id) {
        $SQL = "SELECT * FROM " . self::table() . " WHERE " . static::$primaryKey . " = :id";
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $statement->bindParam('id', $id);
        $statement->execute();
        $object = $statement->fetchObject(get_called_class());
        return $object;
    }


    /**
     * Delete calling entity 
     * 
     * @param int $id
     * @return void
     */
    public function remove(): void {
        $SQL = "DELETE FROM " . self::table() . " WHERE " . static::$primaryKey . " =:id";
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $primaryKeyValue = $this->getPrimaryKeyValue();
        $statement->bindParam("id", $primaryKeyValue);
        if ($statement->execute()) {
            $this->setPrimaryKeyValue(0);
        }
    }


    /**
     * Insert the calling entity
     *
     * @return void
     */
    public function save(): void {
        if ($this->getPrimaryKeyValue() != 0) {
            $values = [];
            foreach (static::$attributes as $column) {
                $values[$column] = $this->{$this->$column};
            }
            $this->update($values);
        }
        $values = [];
        $SQL = "INSERT INTO " . self::table() . " VALUES (";
        foreach (static::$attributes as $index => $attribut) {
            if ($index + 1 == count(static::$attributes)) {
                $SQL .= "?)";
            } else {
                $SQL .= "?,";
            }
            $values[] = $this->{$attribut};
        }
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $code = $statement->execute($values);
        if ($code) {
            $this->setPrimaryKeyValue(Environment::getInstance()->cnx->lastInsertId());
        }
    }

    /**
     * Create the entity with the specified columns
     *
     * @param array $columns
     * @return void
     */
    public static function create(array $columns) {
        $SQL = "INSERT INTO " . self::table() . " (";
        $last_key = end(array_keys($columns));
        $indexed = "(";
        foreach ($columns as $key => $column) {
            if ($key != $last_key) {
                $SQL .= "$key ,";
                $indexed .= "?,";
            } else {
                $SQL .= "$key ) VALUES ";
                $indexed .= "?)";
            }
        }
        $SQL .= $indexed;
        $values = array_values($columns);
        foreach ($values as &$value) {
            $value = htmlspecialchars($value);
        }
        $cnx = Environment::getInstance()->cnx;
        $cnx->setAttribute(\PDO::ATTR_EMULATE_PREPARES, TRUE);
        $statement = $cnx->prepare($SQL);
        $statement->execute($values);
        return get_called_class()::find($cnx->lastInsertId());
    }



    /**
     * Update the calling entity
     * 
     * @param array $columns
     * @return void
     */
    // public function update(array $columns): void {
    //     if ($this->getPrimaryKeyValue() == 0) {
    //         $this->save();
    //     } else {
    //         $class = new \ReflectionClass(get_called_class());
    //         $SQL = "UPDATE " . self::table() . " SET ";
    //         $last_key = end(array_keys($columns));
    //         foreach ($columns as $key => $value) {
    //             if ($key != $last_key) {
    //                 $SQL .= "$key = ?, ";
    //             } else {
    //                 $SQL .= "$key = ? ";
    //             }
    //             if ($class->hasProperty($key)) {
    //                 $this->{$key} = $value;
    //             }
    //         }
    //         $SQL .= "WHERE " . static::$primaryKey . " = ?";
    //         $values = array_values($columns);
    //         foreach ($values as &$value) {
    //             $value = htmlspecialchars($value);
    //         }
    //         $id = $this->getPrimaryKeyValue();
    //         $values[] = $id;
    //         $statement = Environment::getInstance()->cnx->prepare($SQL);
    //         $statement->execute($values);
    //     }
    // }


    /**
     * Return all record for an entity
     *
     * @return array
     */
    public static function all(): Collection {
        $SQL = "SELECT * FROM " . self::table();
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $array = $statement->fetchAll();
        return new Collection($array);
    }



    public function with(...$args) {
        $relationship_index = -1;
        foreach ($args as $index => $arg) {
            $nested_relationships = []; 
            $relations = explode(".", $arg);
            foreach($relations as $index => $relation){
                if($index != 0){
                    $parent = $relations[$index-1];
                    $nested_relationships[$parent][] = $relation;
                }else{
                    $this->{$relation} = call_user_func(array($this, $relation));
                }
            }
            foreach($nested_relationships as $nested_key => $nested_value){
                call_user_func_array(array($this->{$nested_key}, "with"), $nested_value);
            }
        }
        return $this;
    }



    protected static function table($entity = null) {
        $entity = get_called_class();
        if($entity::$table)
            return $entity::$table;
        return parent::table($entity);
    }
}
