<?php

namespace Models;

use Framework\Core\Model;

class Message extends Model
{


    /**
     * Table name
     *
     * @var string
     */
    protected static $table = "messages";


    /**
     * List of all table column
     *
     * @var array
     */
    protected static $attributes = ['type', 'message', 'user_id', 'room_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function room(){
        return $this->belongsTo(Room::class);
    }

    
}
