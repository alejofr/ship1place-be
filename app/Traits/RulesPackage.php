<?php

namespace App\Traits;

trait RulesPackage{
    
    public function rulesPackage()
    {
        return [
            'user_id'  => ['integer'],
            'name'=> ['max:255'],
            'height' => ['numeric','regex:/^[\d]{0,11}(\.[\d]{2})?$/'],
            'long' => ['numeric','regex:/^[\d]{0,11}(\.[\d]{2})?$/'], 
            'width' => ['numeric','regex:/^[\d]{0,11}(\.[\d]{2})?$/'],
            'dimension' => ['max:35'] ,
        ];
    }

    public function isRequiredPackage($data = [])
    {

        $newData = [];

        foreach ($data as $key => $value) {
            $rules = [...$value];

            if( $key != 'dimension'){
                $newData[$key] = [ 'required', ...$rules];
            }else{
                $newData[$key] = [...$rules];
            }
        }

        return $newData;
    }
}