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
    protected static $primaryKey = "user_id";
    
    /**
     * List of all table column
     *
     * @var array
     */
    protected static $attributes = ['nom', 'login', 'password'];
    
    protected $hidden = ["password"]; 

    public function roles(){
        return $this->belongsToMany(Role::class, UserRole::class, 'role_id', 'user_id');
    }



    public static function getJWTIdentifier() : string
    {
        return "login";
    }


}
