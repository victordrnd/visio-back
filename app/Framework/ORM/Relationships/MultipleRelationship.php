<?php


namespace Framework\ORM\Relationships;
use Framework\Core\Contracts\Relationship\RelationshipInterface;

class MultipleRelationship implements RelationshipInterface
{

    public array $queries;
    public function __construct(array $queries)
    {
        $this->queries = $queries;
    }


    public function execute(){
        $values = [];
        foreach($this->queries as $query){
            $values[] = $query->first();
        }
        return $values;
    }
}
