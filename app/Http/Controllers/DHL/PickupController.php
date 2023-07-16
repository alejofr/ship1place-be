<?php

namespace App\Http\Controllers\DHL;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelPickupDHLRequest;
use App\Http\Requests\StorePickupDHLRequest;
use App\Services\DHL\Entity\Pickup;
use App\Services\DHL\ServicePickup;
use App\Services\ShipmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PickupController extends Controller
{

    use ApiResponse;

    private $shipmentService;
    private $pickupServiceDHL;


    public function __construct(ServicePickup $pickupServiceDHL, ShipmentService $serviceShipment)
    {
        $this->pickupServiceDHL = $pickupServiceDHL;
        $this->shipmentService = $serviceShipment;
    }

    /**
     * Cancel DHL Pickup
     *
     * @return Illuminate\Http\Response
    */

    public function cancelPickup($id, CancelPickupDHLRequest $request)
    {
        $shipement = $this->shipmentService->showShipement($id);
        $pickup = json_decode($shipement->pickup_response);

        $dataResponse = $this->pickupServiceDHL->deletePickup($pickup->dispatchConfirmationNumbers[0], $request->all());

        $shipement->pickup_request = null;
        $shipement->pickup_response = null;
        $shipement->pickup_cancel = json_encode($dataResponse);
        $shipement->update();


        return $this->successResponse(['data'=> $dataResponse]);
    }

    /**
     * Generate DHL Pickup
     *
     * @return Illuminate\Http\Response
    */

    public function generatePickup(StorePickupDHLRequest $request)
    {
        $shipement = $this->shipmentService->showShipement($request->shipment_id);
        $service = json_decode($shipement->service);
        
        $data = $request->all();
        $data['plannedPickupDateAndTime'] = $data['plannedPickupDate'].'T'.$data['plannedPickupTime'];
        
        unset($data['shipment_id']);
        unset($data['plannedPickupDate']);
        unset($data['plannedPickupTime']);

        $data['packages'] = json_decode($shipement->pieces);
        $data['shipmentDetails']['productCode'] = $service->productCode;
        $data['shipmentDetails']['shipmentTrackingNumber'] = $shipement->tracking_number;

        $entityPickup = new Pickup($data);
        $dataResponse = $this->pickupServiceDHL->store($entityPickup);

        $shipement->pickup_request = json_encode($entityPickup->getDataArray());
        $shipement->pickup_response = json_encode($dataResponse);
        $shipement->update();

        return $this->successResponse(['data'=> $dataResponse]);
    }
}
