<?php

namespace Framework\Facades;

use Framework\Core\Exceptions\InvalidCredentialsException;
use Framework\Core\Exceptions\InvalidTokenException;
use Framework\Core\Exceptions\ModelNotFoundException;
use Framework\Core\Http\HttpHeader;
use Models\User;
use Models\Permission;

class Auth {

    public function check() {
        return !is_null($this->user());
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
            return User::find($payload['sub']);
        }
        throw new InvalidTokenException();
        return null;
    }



    public static function attempt(array $credentials): mixed {
        $user = User::where(User::getJWTIdentifier(), $credentials[0])->first();
        if (!is_null($user)) {
            if (Hash::check($credentials[1], $user->getPassword())) {
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
