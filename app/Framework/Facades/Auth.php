<?php

namespace Framework\Facades;

use Framework\Core\Exceptions\InvalidCredentialsException;
use Framework\Core\Exceptions\InvalidTokenException;
use Framework\Core\Exceptions\ModelNotFoundException;
use Framework\Core\Http\HttpHeader;
use Models\User;
use Models\Permission;

class Auth {

    private static $user = null;
    public static function check() {
        if(!is_null(self::user())){
            return true;
        }
        throw new InvalidTokenException();
        return false;
    }

    public static function user() {
        $jwt_token = HttpHeader::getBearerToken();
        if (!is_null($jwt_token)) {
            try {
                $payload = JWT::validate($jwt_token);
            } catch (\Exception $e) {
                throw $e;
                return null;
            }

            return User::find($payload->sub);
        }
        return static::$user;
    }



    public static function attempt(array $credentials) {
        if(count($credentials) == 2)
            $user = User::where(User::getJWTIdentifier(), $credentials[0])->first();
            if (!is_null($user)) {
                if (Hash::check($credentials[1], $user->password)) {
                static::$user = $user;
                return JWT::encode(['sub' => $user->getPrimaryKeyValue(), "exp" => time() + (60 * 60 * 24 * 60)]);
            } else {
                throw new InvalidCredentialsException();
                return false;
            }
        } else {
            return false;
        }
    }
}
