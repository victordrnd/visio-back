<?php

namespace Framework\ORM;

use Framework\Core\Singleton;
use Framework\ORM\Relationship;
use Framework\Core\Collection;

abstract class QueryBuilder extends Relationship
{
    /**
     * Find entity with selected id
     *
     * @param int $id
     */
    public static function find(int $id)
    {
        $SQL = "SELECT * FROM " . static::table() . " WHERE " . static::$primaryKey . " = :id";
        $statement = Singleton::getInstance()->cnx->prepare($SQL);
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
    public function remove(): void
    {
        $SQL = "DELETE FROM " . static::table() . " WHERE " . static::$primaryKey . " =:id";
        $statement = Singleton::getInstance()->cnx->prepare($SQL);
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
    public function save(): void
    {
        if ($this->getPrimaryKeyValue() != 0) {
            $values = [];
            foreach (static::$attributes as $column) {
                $values[$column] = $this->{$this->$column};
            }
            $this->update($values);
        }
        $values = [];
        $SQL = "INSERT INTO " . static::table() . " VALUES (";
        foreach (static::$attributes as $index => $attribut) {
            if ($index + 1 == count(static::$attributes)) { 
                $SQL .= "?)";
            } else {
                $SQL .= "?,";
            }
            $values[] = $this->{$attribut};
        }
        $statement = Singleton::getInstance()->cnx->prepare($SQL);
        $code = $statement->execute($values);
        if ($code) {
            $this->setPrimaryKeyValue($this->cnx->lastInsertId());
        }
    }

    /**
     * Create the entity with the specified columns
     *
     * @param array $columns
     * @return void
     */
    public static function create(array $columns)
    {
        $SQL = "INSERT INTO " . static::table() . " (";
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
        $cnx = Singleton::getInstance()->cnx;
        $cnx->setAttribute(\PDO::ATTR_EMULATE_PREPARES, TRUE);
        $statement = $cnx->prepare($SQL);
        $statement->execute($values);
        $last_insert = get_called_class()::last();
        return get_called_class()::find($last_insert->getPrimaryKeyValue());
    }



    /**
     * Update the calling entity
     * 
     * @param array $columns
     * @return void
     */
    public function update(array $columns): void
    {
        if ($this->getPrimaryKeyValue() == 0) {
            $this->save();
        } else {
            $class = new \ReflectionClass(get_called_class());
            $SQL = "UPDATE " . static::table() . " SET ";
            $last_key = end(array_keys($columns));
            foreach ($columns as $key => $value) {
                if ($key != $last_key) {
                    $SQL .= "$key = ?, ";
                } else {
                    $SQL .= "$key = ? ";
                }
                if ($class->hasProperty($key)) {
                    $this->{$key} = $value;
                }
            }
            $SQL .= "WHERE " . static::$primaryKey . " = ?";
            $values = array_values($columns);
            foreach ($values as &$value) {
                $value = htmlspecialchars($value);
            }
            $id = $this->getPrimaryKeyValue();
            $values[] = $id;
            $statement = Singleton::getInstance()->cnx->prepare($SQL);
            $statement->execute($values);
        }
    }


    /**
     * Return all record for an entity
     *
     * @return array
     */
    public static function all(): array
    {
        $SQL = "SELECT * FROM " . static::table();
        $statement = Singleton::getInstance()->cnx->prepare($SQL);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $array = $statement->fetchAll();
        return $array;
    }



    /**
     *
     * @return integer
     */
    public static function count(): int
    {
        $SQL = "SELECT COUNT(*) as count FROM " . static::table();
        $statement = Singleton::getInstance()->cnx->prepare($SQL);
        $statement->execute();
        $res = $statement->fetchObject();
        return $res->count;
    }


    /**
     * Return array of Object with specified conditions
     *
     * @param [type] $column
     * @param [type] $value
     * @return void
     */
    public static function where($column, $operator = "=", $value = null)
    {
        $operators = ['=', '>=', '>', '<', '<=', '!=', 'LIKE','NOT LIKE'];
        if (in_array($operator, $operators)) {
            $SQL = "SELECT * FROM " . static::table() . " WHERE " . $column . " " . $operator . " ?";
        } else {
            $SQL = "SELECT * FROM " . static::$table . " WHERE " . $column . " = ?";
            $value = $operator;
        }
        $statement = Singleton::getInstance()->cnx->prepare($SQL);
        $statement->execute(array($value));
        $statement->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $array = $statement->fetchAll();
        return new Collection($array);
    }


    /**
     * Return last inserted Object from calling class
     *
     * @return void
     */
    public static function last()
    {
        $SQL = "SELECT * FROM " . static::$table . " ORDER BY " . static::$primaryKey . " DESC LIMIT 1";
        $statement = Singleton::getInstance()->cnx->prepare($SQL);
        $statement->execute();
        $object = $statement->fetchObject(get_called_class());
        return $object;
    }
}
