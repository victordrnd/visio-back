<?php

namespace Models;

use Framework\Core\Model;

class UserRoom extends Model
{


    /**
     * Table name
     *
     * @var string
     */
    protected static $table = "user_rooms";


    /**
     * List of all table column
     *
     * @var array
     */
    protected static $attributes = ['user_id', 'room_id', 'last_read'];
}
