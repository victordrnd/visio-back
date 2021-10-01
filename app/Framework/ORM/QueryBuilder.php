<?php

namespace Framework\ORM;

use Exception;
use Framework\Core\Environment;
use Framework\ORM\Relationship;
use Framework\Core\Collection;
use Framework\Core\Exceptions\RelationNotFoundException;
use Framework\ORM\Query\BaseQuery;

class QueryBuilder extends BaseQuery
{
    use Relationship;

    /**
     * Find entity with selected id
     *
     * @param int $id
     */
    public static function find(int $id)
    {
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
    public static function destroy($id)
    {
        $SQL = "DELETE FROM " . self::table() . " WHERE " . static::$primaryKey . " =:id";
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        return $statement->bindParam("id", $id);
    }


    /**
     * Insert the calling entity
     *
     * @return void
     */
    public function save(): bool
    {
        $values = [];
        foreach (static::$attributes as $column) {
            $values[$column] = $this->{$column};
        }
        if ($this->getPrimaryKeyValue() != 0) {
            return !is_null($this->update($values)) ? true : null;
        } else {
            $item = self::create($values);
            if (!is_null($item)) {
                $this->setPrimaryKeyValue($item->getLastInsertedId());
                return true;
            }
            return false;
        }
    }


    /**
     * Update the calling entity
     * 
     * @param array $columns
     * @return void
     */
    public function fill(array $columns): void
    {
        $class = new \ReflectionClass(get_called_class());
        foreach ($columns as $key => $value) {
            if ($class->hasProperty($key)) {
                $this->{$key} = $value;
            }
        }
    }


    /**
     * Return all record for an entity
     *
     * @return array
     */
    public static function all(): Collection
    {
        $SQL = "SELECT * FROM " . self::table();
        $statement = Environment::getInstance()->cnx->prepare($SQL);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $array = $statement->fetchAll();
        return new Collection($array);
    }



    public function load(...$args)
    {
        if (!(isset($this)))
            return parent::with(...$args);

        foreach ($args as $index => $arg) {
            $nested_relationships = [];
            $relations = explode(".", $arg);
            foreach ($relations as $index => $relation) {
                if ($index != 0) {
                    $parent = $relations[$index - 1];
                    $nested_relationships[$parent][] = $relation;
                } else {
                    if (method_exists($this, $relation)) {
                        $this->{$relation} = call_user_func(array($this, $relation))->execute();
                    } else {
                        throw new RelationNotFoundException($relation, get_class($this));
                    }
                }
            }
            foreach ($nested_relationships as $nested_key => &$nested_value) {
                $nested_value = isset($nested_value[0]) ? $nested_value[0] : $nested_value;
                if (!($this->{$nested_key} instanceof Collection)) {
                    call_user_func_array(array($this->{$nested_key}, "load"), $nested_value);
                }
            }
        }
        return $this;
    }



    protected static function table($entity = null)
    {
        $entity = get_called_class();
        if ($entity::$table)
            return $entity::$table;
        return parent::table($entity);
    }
}
