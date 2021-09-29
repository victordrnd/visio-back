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
     * @param class $entity
     * @param class $AssociationEntity
     * @param [type] $foreign_key
     * @param [type] $localkey
     * @return void
     */
    protected function hasManyThrough($entity, $AssociationEntity,string $foreign_key,string $localkey)
    {
        $values = [];
        $AssociationValues = $AssociationEntity::where($localkey, $this->{$localkey})->get();
        foreach($AssociationValues as $AssociationRow){
            $values[] = $entity::find($AssociationRow->{$foreign_key});
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
