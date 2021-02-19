<?php
namespace Framework\Facades;

use Models\User;
use Models\Permission;

class Auth
{




    /**
     * Verify if user have permission
     *
     * @param [type] $permission
     * @return boolean
     */
    public static function has($permission): bool
    {
    
    }



    public static function attempt(array $credentials): bool
    {
        $user = User::where('login', $credentials[0])->with('roles')->first();
        if (!is_null($user)) {
            if (password_verify($credentials[1], $user->getPassword())) {
                self::log($user);
                return true;
            } else {
                throw new \Exception("Le mot de passe saisis est incorrect");
            }
        } else {
            throw new \Exception("L'utilisateur n'existe pas");
        }
    }
}
