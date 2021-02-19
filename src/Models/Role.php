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



    public function getRoleId(){
        return $this->role_id;
    }

    public function getLibelle(){
        return $this->libelle;
    }

    public function getPermissions(){
        return $this->permissions;
    }


    public function setPermissions($permissions){
        $this->permissions = $permissions;
    }

}


?>