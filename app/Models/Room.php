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
}
