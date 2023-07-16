<?php


namespace App\Traits;

use App\Rules\IsObject;

trait RulesCustomer{
    
    public function rulesCustomer()
    {
        return [
            'user_id'  => ['integer'],
            'province_id'  => ['integer'],
            'country_id'  => ['integer'],
            'city_id'  => ['integer'],
            'name' => ['max:35'],
            'contact_name' => ['max:60'],
            'email' => ['max:255', 'email'],
            'address1' => ['max:120'],
            'address2' =>  ['max:120'],
            'zip' => ['max:12'],
            'phone' => ['max:25'],
            'isSender' => ['boolean'],
            'extra' => [new IsObject]
        ];
    }

    public function isRequiredCustomer($data = [])
    {
        $rulesNot = [
            'email',
            'address2',
            'province_id',
            'extra'
        ];

        $newData = [];

        foreach ($data as $key => $value) {
            $rules = [...$value];

            if( !in_array($key, $rulesNot)){
                $newData[$key] = [ 'required', ...$rules];
            }else{
                $newData[$key] = [...$rules];
            }

            
        }

        return $newData;
    }
}