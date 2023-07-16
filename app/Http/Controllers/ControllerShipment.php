<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexShipementRequest;
use App\Services\CustomerService;
use App\Services\ShipmentService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ControllerShipment extends Controller
{   
    use ApiResponse;
    private $userService;
    private $customerService;
    private $shipmentService;

    /**
     * Create a new controller instance.
     *
     * @return void
    */

    public function __construct(UserService $userService, CustomerService $customerService, ShipmentService $serviceShipment)
    {
        $this->userService =  $userService;
        $this->customerService = $customerService;
        $this->shipmentService = $serviceShipment;
    }

     /**
     * Return Shipements LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function index(IndexShipementRequest $request)
    {
        $this->userService->showUser($request->user_id);

        return $this->successResponse($this->shipmentService->index($request->limit, $request->page,$request->orderBy, $request->ascending, $request->user_id, $request->service, $request->waybill, $request->dateFrom, $request->dateTo, $request->status));
    }

    
    /**
      * Return an especify Shipement
      *
      * @return  Illuminate\Http\Response
    */
    
    public function show($id)
    {
        $shipement = $this->shipmentService->showShipement($id);

        $sender = $this->customerService->showCustomer($shipement->sender_id);
        $receiver =  $this->customerService->showCustomer($shipement->receiver_id);

        $sender->extra = json_decode($sender->extra);
        $receiver->extra = json_decode($receiver->extra);
        $shipement->service = json_decode($shipement->service);
        $shipement->pieces = json_decode($shipement->pieces);
        $shipement->shipment_response = json_decode($shipement->shipment_response);

        unset($shipement->sender_id);
        unset($shipement->receiver_id);

        $shipement->sender = $sender;
        $shipement->receiver = $receiver;

        return $this->successResponse(['data'=> $shipement]);
    }

    /**
      * Removes an existing Shipement
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $shipement = $this->shipmentService->showShipement($id);

        if( $shipement->status == 'paid' ){
            return $this->errorResponse("delete cancelled, can not do it for shipement id $id", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $shipement->status = 'canceled';
        $shipement->update();

        return $this->successResponse(['data'=> $shipement]);
    }
}
