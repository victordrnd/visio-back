<?php


namespace Framework\ORM\Relationships;

use Framework\Core\Contracts\Relationship\RelationshipInterface;
use Framework\ORM\Query\BaseQuery;

class UniqueRelationship implements RelationshipInterface
{

    public BaseQuery $query;
    private $is_collection;
    public $entity;

    public function __construct(BaseQuery $query, $collection = false)
    {
        $this->query = $query;
        $this->entity = $query->entity;
        $this->is_collection = $collection;
    }


    public function execute()
    {
        if ($this->is_collection) {
            return $this->query->get();
        }
        return $this->query->first();
    }


    public function __get(string $name)
    {
        if (method_exists($this->query, $name)) {
            return call_user_func(array($this->query, $name));
        }
        return NULL;
    }

    public function __call($name, $arguments)
    {
        if($name != "execute"){
            if (method_exists($this->query, $name)) {
                call_user_func_array(array($this->query, $name), $arguments);
                return $this;
            }
        }
        return $this;
    }
}
