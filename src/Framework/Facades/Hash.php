<?php

namespace Framework\Facades;

class Hash{
    
    public static function make($text){
        return password_hash($text, PASSWORD_BCRYPT, ['cost' => 6]);
    }


    public static function check(string $value, string $hashedValue){
        return password_verify($value, $hashedValue);
    }

}