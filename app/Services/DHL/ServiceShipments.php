<?php

namespace App\Services\DHL;
use App\Services\DHL\ApiDHL;
use App\Services\DHL\Entity\AccountsDHL;
use App\Services\DHL\Entity\Shipment;

class ServiceShipments{

    public function getImage($shipmentTrackingNumber, $typeCode, $pickupYearAndMonth)
    { 
        $data = [
            'typeCode' => $typeCode,
            'pickupYearAndMonth' => $pickupYearAndMonth,
            'shipperAccountNumber' => AccountsDHL::numberAccounts()[0]['number']
        ];

        $request = new ApiDHL("shipments/$shipmentTrackingNumber/get-image");

        return $request->get($data);
    }

    public function store(Shipment $shipment)
    {
        $data = $shipment->getDataArray();

        $request = new ApiDHL('shipments');

        return $request->post($data);
    }
}