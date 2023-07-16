<?php

namespace App\Services\DHL\Entity;

final class AccountsDHL{
    public static function numberAccounts()
    {
        return [
            [
                'typeCode' => 'shipper',
                'number' => env('ACCOUNTS_NUMBER_DHL')
            ]
        ];
    }
}