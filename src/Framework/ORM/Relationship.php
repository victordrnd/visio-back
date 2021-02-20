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
    protected function hasOne($entity, string $localkey, string $foreign_key)
    {
        return $entity::where($foreign_key, $this->{$localkey})->first();
    }



    /**
     * One to One
     *
     * @param class $entity
     * @param string $localkey -> City -> CountryCode
     * @param string $foreign_key -> Country -> Code
     * @return void
     */
    protected function belongsTo($entity, string $localkey, string $foreign_key)
    {
        return $entity::where($foreign_key, $this->{$localkey})->first();
    }





    /**
     * One to Many
     *
     * @param class $entity
     * @param string $localkey
     * @param string $foreign_key
     * @return boolean
     */
    protected function hasMany($entity, string $localkey, string $foreign_key)
    {
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
    protected function belongsToMany($entity, $AssociationEntity,string $foreign_key,string $localkey)
    {
        $values = [];
        $AssociationValues = $AssociationEntity::where($localkey, $this->{$localkey})->get();
        foreach($AssociationValues as $AssociationRow){
            $values[] = $entity::find($AssociationRow->{$foreign_key});
        }
        return $values;
    }

}
