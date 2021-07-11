<?php

namespace Framework\Facades;

use Framework\Core\Environment;
use Framework\Core\Exceptions\ExpiredTokenException;
use Framework\Core\Exceptions\InvalidTokenException;

class JWT {


    public static function encode($payload): string {
        $env = Environment::getInstance();
        try {
            $secret = $env->getConfigValue("JWT_SECRET");
        } catch (\Exception $e) {
            $secret = bin2hex(random_bytes(32));
            $env->setConfigValue("JWT_SECRET", $secret);
        }
        //Header
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS512'
        ]);
        $base64UrlHeader = self::base64UrlEncode($header);

        //Payload
        $payload = json_encode($payload);
        $base64UrlPayload = self::base64UrlEncode($payload);

        //Signature
        $signature = hash_hmac('sha512', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }


    public static function validate($jwt_token) {
        $env = Environment::getInstance();
        $secret = $env->getConfigValue("JWT_SECRET");
        $parts = explode('.', $jwt_token);
        $header = base64_decode($parts[0]);
        $payload = base64_decode($parts[1]);
        $signatureProvided = $parts[2];
        $decoded_payload = json_decode($payload);
        if($decoded_payload->exp <= time()){
            throw new ExpiredTokenException();
            return false;
        }

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        $signature = hash_hmac('sha512', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        if($base64UrlSignature === $signatureProvided)
            return $decoded_payload;
        throw new InvalidTokenException();  
        return false;
    }



    private static function base64UrlEncode($text) {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }
}
