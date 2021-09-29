<?php


namespace Framework\ORM;

trait Relationship
{

    /**
     * Undocumented function
     *
     * @param class $entity
     * @param string $localkey
     * @param string $foreign_key
     * @return boolean
     */
    protected function hasOne($entity, string $foreign_key = "", string $localkey ="")
    {
        if(empty($foreign_key))
            $this->generateKeyName(get_called_class());
        if(empty($localkey))
            $localkey = get_called_class()::$primaryKey;
        return $entity::where($foreign_key, $this->{$localkey})->first();
    }



    /**
     * One to One
     *
     * @param string $entity
     * @param string $localkey -> City -> CountryCode
     * @param string $foreign_key -> Country -> Code
     * @return void
     */
    protected function belongsTo(string $entity, string $local_key = "", string $related_key = "id")
    {
        if(empty($local_key))
            $this->generateKeyName($entity);
        return $entity::where($related_key, $this->{$local_key})->first();
    }





    /**
     * One to Many
     *
     * @param class $entity
     * @param string $localkey
     * @param string $foreign_key
     * @return boolean
     */
    protected function hasMany($entity, string $localkey = "", string $foreign_key = "")
    {
        if(empty($foreign_key))
            $this->generateKeyName(get_called_class());
        if(empty($localkey))
            $localkey = get_called_class()::$primaryKey;
        return $entity::where($foreign_key, $this->{$localkey})->get();
    }


    /**
     * Many to Many
     *
     * @param string $entity
     * @param class $AssociationEntity
     * @param [type] $foreign_key
     * @param [type] $localkey
     * @return void
     */
    protected function hasManyThrough(string $entity, $associationEntity,string $foreign_key ="",string $association_related_key ="", string $localkey = "", string $relatedkey ="")
    {
        if(empty($localkey))
            $localkey = get_called_class()::$primaryKey;
        
        if(empty($relatedkey))
            $relatedkey = $entity::$primaryKey;

        if(empty($foreign_key))
            $foreign_key = $this->generateKeyName($entity);
        if(empty($association_related_key))
            $association_related_key = $this->generateKeyName(get_called_class());
        $values = [];
        $associationValues = $associationEntity::where($association_related_key, $this->{$localkey})->get();
        foreach($associationValues as $associationRow){
            $values[] = $entity::where($relatedkey, $associationRow->{$foreign_key})->first();
        }
        return $values;
    }


    private function generateKeyName(string $entity) : string {
        $class = explode('\\', $entity);
        $key_name = strtolower(array_pop($class));
        $key_name.= "_id";
        return $key_name;
    }

}
