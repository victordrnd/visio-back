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
        var_dump($this->entity);
        $this->is_collection = $collection;
    }


    public function execute(){
        if($this->is_collection){
            return $this->query->get();
        }
        return $this->query->first();
    }
}
