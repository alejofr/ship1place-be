<?php

namespace App\Http\Controllers\DHL;

use App\Helpers\FormaterDataCustomer;
use App\Helpers\getIdUserClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetImageDHLShipementRequest;
use App\Http\Requests\StoreShipmentDHLRequest;
use App\Services\CityService;
use App\Services\CountryService;
use App\Services\CustomerService;
use App\Services\DHL\Entity\Shipment;
use App\Services\DHL\ServiceShipments as ServiceShipmentDHL;
use App\Services\Log\CreateLogRequest;
use App\Services\ProvinceService;
use App\Services\ShipmentService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    use ApiResponse;
    private $serviceShipmentDHL;
    private $userService;
    private $customerService;
    private $shipmentService;
    private $provinceService;
    private $countryService;
    private $cityService;

    public function __construct(ServiceShipmentDHL $serviceShipmentDHL, UserService $userService, CustomerService $customerService, ShipmentService $serviceShipment, ProvinceService $provinceService, CountryService $countryService, CityService $cityService)
    {
        $this->serviceShipmentDHL = $serviceShipmentDHL;
        $this->userService =  $userService;
        $this->customerService = $customerService;
        $this->shipmentService = $serviceShipment;
        $this->provinceService = $provinceService;
        $this->countryService = $countryService;
        $this->cityService = $cityService;
    }

    /**
     * DHL shipement get pdf
     * 
     * @return Illuminate\Http\Response
    */

    public function getImage($id, GetImageDHLShipementRequest $request)
    {
        $shipement = $this->shipmentService->showShipement($id);

        $data =  $this->serviceShipmentDHL->getImage($shipement->tracking_number, $request->typeCode, $request->pickupYearAndMonth);

        return $this->successResponse(['data'=> $data]);
    }

    /**
     * DHL shipement and shipement creation
     * 
     * @return Illuminate\Http\Response
    */

    public function store(StoreShipmentDHLRequest $request)
    {
        $data = $request->all();

        $user = $request->user(); // get user auth

        // check  location shipper or receiver
        $this->countryService->showCountry($data['shipperDetails']['postalAddress']['country_id']);
        $this->cityService->showCity($data['shipperDetails']['postalAddress']['city_id']);
        $this->countryService->showCountry($data['receiverDetails']['postalAddress']['country_id']);
        $this->cityService->showCity($data['receiverDetails']['postalAddress']['city_id']);

        if (isset($data['shipperDetails']['postalAddress']['province_id'])) {
            $this->provinceService->showPronvince($data['shipperDetails']['postalAddress']['province_id']);
        }

        if (isset($data['receiverDetails']['postalAddress']['province_id'])) {
            $this->provinceService->showPronvince($data['receiverDetails']['postalAddress']['province_id']);
        }

        //create shipment DHL
        $entityShipment = new Shipment($data);
        $dataResponse = $this->serviceShipmentDHL->store($entityShipment);

        $user_id = getIdUserClient::getIdUser($user);
        $sender = FormaterDataCustomer::formaterDataInShipment($data['shipperDetails'], true, $user_id);
        $receiver = FormaterDataCustomer::formaterDataInShipment($data['receiverDetails'], false, $user_id);
        
        
        // get o store customer sender or receiver
        $sender = $this->customerService->storCustomerForSenderOrReceiver($sender);
        $receiver = $this->customerService->storCustomerForSenderOrReceiver($receiver);

        // create shipment 

        $dataStoreShipement = $this->shipmentService->storeShipement([
            'user_id' => $user->user_id,
            'sender_id' => $sender->customer_id,
            'receiver_id' => $receiver->customer_id,
            'service_name' => 'dhl',  // dhl 
            'price' => $data['price'],
            'currency' => $data['currency'],
            'tracking_number' => $dataResponse['shipmentTrackingNumber'],
            'service' => $data['service'],
            'pieces' => $data['packages'],
            'shipment_response' => $dataResponse,
            's_date' => $data['plannedShippingDateAndTime'].' '.date('H:i:s')
        ]);

        $log = new CreateLogRequest('create',null); // instance log for created shipment

        $dataStoreShipement->service = json_decode($dataStoreShipement->service);
        $dataStoreShipement->pieces = json_decode($dataStoreShipement->pieces);
        $dataStoreShipement->shipment_response = json_decode($dataStoreShipement->shipment_response);

        $log->createLog($dataStoreShipement); // save data shipment
        
        return $this->successResponse(['data'=> $dataStoreShipement]);
    }
}
