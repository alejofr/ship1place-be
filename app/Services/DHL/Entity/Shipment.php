<?php

namespace App\Services\DHL\Entity;

final class Shipment{
    public $accounts;
    public $plannedShippingDateAndTime;
    public $pickup;
    public $productCode;
    public $customerDetails;
    public $content;
    public $extras = [];

    public function __construct($data = [])
    {
        $this->accounts = [ 'accounts' => AccountsDHL::numberAccounts() ];

        unset($data['price']);
        unset($data['service']);
        unset($data['currency']);
        unset($data['shipperDetails']['postalAddress']['country_id']);
        unset($data['shipperDetails']['postalAddress']['city_id']);
        unset($data['receiverDetails']['postalAddress']['country_id']);
        unset($data['receiverDetails']['postalAddress']['city_id']);

        if (isset($data['shipperDetails']['postalAddress']['province_id'])) {
            unset($data['shipperDetails']['postalAddress']['province_id']);
        }

        if (isset($data['receiverDetails']['postalAddress']['province_id'])) {
            unset($data['receiverDetails']['postalAddress']['province_id']);
        }

        $this->plannedShippingDateAndTime = $data['plannedShippingDateAndTime'].'T'.date('H:i:s').' GMT+00:00';
        $this->pickup = $data['pickup'];
        $this->productCode = $data['productCode'];    
        $this->customerDetails = [
            'shipperDetails' => $data['shipperDetails'],
            'receiverDetails' => $data['receiverDetails']
        ];

        $this->content = [
            ...$data['content'],
            'packages' => $data['packages']
        ]; 

        foreach ($data as $key => $value) {
            if( !in_array($key, ['plannedShippingDateAndTime', 'pickup', 'productCode', 'shipperDetails', 'receiverDetails', 'content', 'packages']) ){
                $this->extras[$key] = $data[$key];
            }
        }

        $this->extras = [...$this->extras, ...$this->accounts];
    }

    public function getDataArray()
    {
        return [
            'customerDetails' => $this->customerDetails,
            'plannedShippingDateAndTime' => $this->plannedShippingDateAndTime,
            'pickup' => $this->pickup,
            'productCode' => $this->productCode,
            'content' => $this->content,
            ...$this->extras
        ];
    }
}