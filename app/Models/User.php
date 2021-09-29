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

    // public function roles(){
    //     return $this->hasManyThrough(Role::class, UserRole::class, 'role_id', 'user_id');
    // }


    public function room(){
        return $this->hasMany(UserRoom::class);
    }

    public static function getJWTIdentifier() : string
    {
        return "login";
    }


}
