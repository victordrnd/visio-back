<?php


namespace Framework\ORM\Relationships;
use Framework\Core\Contracts\Relationship\RelationshipInterface;

class MultipleRelationship implements RelationshipInterface
{

    public array $children_queries;
    public UniqueRelationship $query;
    public function __construct(array $children_queries, UniqueRelationship $parent_query)
    {
        $this->query = $parent_query;
        $this->children_queries = $children_queries;
    }


    public function execute(){
        $values = [];
        foreach($this->children_queries as $query){
            $values[] = $query->first();
        }
        return $values;
    }
}
