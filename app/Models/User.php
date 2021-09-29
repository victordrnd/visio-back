<?php

namespace Models;

use Framework\Core\Model;

class User extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected static $table = "users";
    
    
    /**
     * Table primary key
     *
     * @var string
     */
    protected static $primaryKey = "id";
    
    /**
     * List of all table column
     *
     * @var array
     */
    protected static $attributes = ['firstname', 'lastname','email', 'password'];
    
    protected $hidden = ["password"]; 


    // public function rooms(){
    //     return $this->hasMany(UserRoom::class);
    // }

    public function rooms(){
        return $this->hasManyThrough(Room::class, UserRoom::class);
    }

    public static function getJWTIdentifier() : string
    {
        return "email";
    }


}
