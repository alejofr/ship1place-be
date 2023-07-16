<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class PropertyReq implements ValidationRule
{
    public $dataValid = [];
    public $property;
    public $isObject;
    public $msg;
    public $subDataValid;

    public function __construct($data = [], $property = "", $isObject = true, $subDataValid = [])
    {
        $this->dataValid = $data;
        $this->property = $property;
        $this->isObject = $isObject;
        $this->subDataValid = $subDataValid;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if( $this->isObject ){
            if( !$this->validCustom($value, $this->dataValid) ){
                $fail($this->msg);
            }
        }else{
            if( is_array($value) ){
                foreach ($value as $key => $val) {
                    if( is_array($value[$key]) ){
                        if( !$this->validCustom($value[$key], $this->dataValid) ){
                            $fail($this->msg);
                        }
    
                        if( count($this->subDataValid) > 0 ){
                            $item =  json_decode(json_encode($value[$key]));
    
                            foreach ($item as $clave => $valor) {
                                if( array_key_exists($clave, $this->subDataValid) ){
                                    
                                    if( !$this->validCustom($value[$key][$clave], $this->subDataValid[$clave]) ){
                                        $fail($this->msg.' and '.$clave);
                                    }
                                }
                            }
                        }
                    }else{
                        $fail('The :attribute must be an array.');
                    }
                }
            }
            
        }
        
    }

    public function validCustom($value, $data = [])
    {

        $validator = Validator::make($value, $data);

        if ( isset($validator) && $validator->fails() ) {
            $this->msg = $validator->errors()->first()."On the property ".$this->property;
            return false;
        }

        return true;
    }
}
