<?php
namespace Models;

use Framework\Core\Model;
class Role extends Model{


    /**
     * Table name
     *
     * @var string
     */
    protected static $table = "roles";


    /**
     * Table primary key
     *
     * @var string
     */
    protected static $primaryKey = "role_id";

    /**
     * List of all table column
     *
     * @var array
     */
    protected static $attributes = [
        'user_id', 'libelle', 'permissions'
    ];
}


?>