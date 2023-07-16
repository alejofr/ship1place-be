<?php

namespace App\Services\DHL;

class ServiceTracking{

    public function getTracking($shipmentTrackingNumber,$data = [])
    { 
        $request = new ApiDHL("shipments/$shipmentTrackingNumber/tracking");

        return $request->get($data);
    }
}