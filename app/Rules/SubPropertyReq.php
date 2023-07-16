<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class SubPropertyReq implements ValidationRule
{
    public $dataValid = [];
    public $property;
    public $msg;

    public function __construct($data, $property)
    {
        $this->dataValid = $data;
        $this->property = $property;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if( is_array($value) && array_key_exists($this->property, $value) ){
            if( is_array($value[$this->property])  ){
                if( !$this->validCustom($value[$this->property], $this->dataValid) ){
                    $fail($this->msg);
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
