<?php

namespace App\Helpers;

class FormaterDataCustomer{
    public static function formaterDataInShipment($data = [], $isSender = true, $user_id = "")
    {
        $dataExtras = [];

        foreach ($data as $key => $value) {
            if( $key != 'postalAddress' && $key != 'contactInformation' ){
                array_push($dataExtras, [$key => $data[$key] ] );
            }
        }

        return [
            'isSender' => $isSender ? 1 : 0,
            'user_id' => $user_id,
            'country_id' => $data['postalAddress']['country_id'],
            'province_id' => isset($data['postalAddress']['province_id']) ? $data['postalAddress']['province_id'] : null,
            'city_id' => $data['postalAddress']['city_id'],
            'name' => $data['contactInformation']['fullName'],
            'contact_name' => $data['contactInformation']['companyName'],
            'email' => isset($data['contactInformation']['email']) ? $data['contactInformation']['email'] : null,
            'address1' => $data['postalAddress']['addressLine1'],
            'address2' => isset($data['postalAddress']['addressLine2']) ? $data['postalAddress']['addressLine2'] : null,
            'zip' => $data['postalAddress']['postalCode'],
            'phone' => $data['contactInformation']['phone'],
            'extra' => json_encode($dataExtras)
        ];
    }
}