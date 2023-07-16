<?php

namespace App\Services\DHL\Entity;

class Pickup{
    public $accounts;
    public $plannedPickupDateAndTime;
    public $customerDetails;
    public $shipmentDetails;
    public $extras = [];

    public function __construct($data = [])
    {
        $this->accounts = [ 'accounts' => AccountsDHL::numberAccounts() ];

        $this->plannedPickupDateAndTime = $data['plannedPickupDateAndTime'].' GMT+08:00';

        $this->customerDetails = [
            'shipperDetails' => $data['shipperDetails'],
            'receiverDetails' => $data['receiverDetails']
        ];

        $this->shipmentDetails = [
            'accounts' => AccountsDHL::numberAccounts(),
            'packages' => $data['packages'],
            ...$data['shipmentDetails']
        ]; 

        foreach ($data as $key => $value) {
            if( !in_array($key, ['plannedPickupDateAndTime', 'shipperDetails', 'receiverDetails', 'packages', 'shipmentDetails']) ){
                $this->extras[$key] = $data[$key];
            }
        }

        $this->extras = [...$this->extras, ...$this->accounts];

    }

    public function getDataArray()
    {
        return [
            'plannedPickupDateAndTime' => $this->plannedPickupDateAndTime,
            'customerDetails' => $this->customerDetails,
            'shipmentDetails' => [$this->shipmentDetails],
            ...$this->extras
        ];
    }
}