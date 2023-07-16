<?php

namespace App\Helpers;

class validJsonProperty{
    public static function isValid($value)
    {
        $val =  json_decode($value);

        if( is_object($val) ){
            return true;
        }

        return false;
    }
}