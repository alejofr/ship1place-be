<?php

namespace App\Services\DHL;
use App\Services\DHL\ApiDHL;
use App\Services\DHL\Entity\Pickup;

class ServicePickup{

    public function deletePickup($number, $data)
    {
        $request = new ApiDHL("pickups/$number");

        return $request->delete($data);
    }
    
    public function store(Pickup $pickup)
    {
        $data = $pickup->getDataArray();

        $request = new ApiDHL('pickups');

        return $request->post($data);
    }
}