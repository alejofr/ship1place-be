<?php

namespace App\Services\DHL;
use App\Services\DHL\ApiDHL;

class ServiceGetRates{
    public $customerDetails;
    public $plannedShippingDateAndTime;
    public $unitOfMeasurement; // "metric" or "imperial"
    public $isCustomsDeclarable = false;
    public $packages;

    public $extras = [
        'estimatedDeliveryDate' => [
            'isRequested' => true,
            'typeCode' => 'QDDC'
        ],
    ];

    public function __construct($customerDetails, $plannedShippingDateAndTime, $unitOfMeasurement = 'metric', $packages = [], $isCustomsDeclarable = false, $extras = [])
    {
        $this->customerDetails = $customerDetails;
        $this->plannedShippingDateAndTime = $plannedShippingDateAndTime;
        $this->unitOfMeasurement = $unitOfMeasurement;
        $this->packages = $packages;
        $this->isCustomsDeclarable = $isCustomsDeclarable;
        $this->extras = [
            ...$this->extras,
            'accounts' => [
                [
                  'typeCode' => 'shipper',
                  'number' => env('ACCOUNTS_NUMBER_DHL')
                ]
            ],
            ...$extras
        ];
    }

    public function getRates()
    {
        $body = [
            'customerDetails' => $this->customerDetails,
            'plannedShippingDateAndTime' => $this->plannedShippingDateAndTime,
            'isCustomsDeclarable' => $this->isCustomsDeclarable,
            'unitOfMeasurement' => $this->unitOfMeasurement,
            'packages' => $this->packages,
            ...$this->extras
        ];

        $request = new ApiDHL('rates');

        return $request->post($body); 
    }
}