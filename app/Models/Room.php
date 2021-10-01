<?php

namespace Models;

use Framework\Core\Model;

class Room extends Model
{


    /**
     * Table name
     *
     * @var string
     */
    protected static $table = "rooms";


    /**
     * List of all table column
     *
     * @var array
     */
    protected static $attributes = ['label', 'picture'];


    public function messages(){
        return $this->hasMany(Message::class)->limit(30)->orderBy('id', 'DESC');
    }

    public function users(){
        return $this->hasMany(UserRoom::class)->select('user_id');
    }



}
