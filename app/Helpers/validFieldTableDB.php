<?php

namespace App\Helpers;

use Illuminate\Validation\ValidationException;

class validFieldTableDB{
    public static function valid($fields = [], $field = '')
    {
        if( in_array($field, $fields) ){
            return true;
        }

        return false;        
    }
}