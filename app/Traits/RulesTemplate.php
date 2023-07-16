<?php

namespace App\Traits;

trait RulesTemplate{
    public function rulesTemplate()
    {
        return [
            'user_id'  => ['integer'],
            'sender_id'  => ['integer'],
            'receiver_id'  => ['integer'],
            'name' => ['max:85'],
            'pieces' => ['array']
        ];
    }

    public function isRequiredTemplate($data = [])
    {

        $newData = [];

        foreach ($data as $key => $value) {
            $rules = [...$value];
            $newData[$key] = [ 'required', ...$rules];
        }

        return $newData;
    }
}