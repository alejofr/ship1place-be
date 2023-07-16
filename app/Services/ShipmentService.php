<?php

namespace App\Services;

use App\Models\Shipment;
use App\Repositories\ShipmentRepository;

class ShipmentService{

    /**
     *  The shipmentRepository for consuming the repository Shipement
    */

    private $shipmentRepository;

    public $fields = [
        'shipments.shipment_id',
        'shipments.user_id',
        'shipments.sender_id',
        'senders.name AS sender_name',
        'shipments.receiver_id',
        'receivers.name AS receiver_name',
        'shipments.service_name',  // dhl, fedex, purolator  information
        'shipments.price',
        'shipments.currency',
        'shipments.tracking_number',
        'shipments.service',
        'shipments.pieces',
        'shipments.shipment_response',
        'shipments.status',  // pendiente por pagar ( outstanding ), pagada ( paid ) y  cancelada ( canceled )
        'shipments.s_date',
    ];


    /**
     * Create a new service instance.
     *
     * @return void
    */

    public function __construct(ShipmentRepository $shipmentRepository)
    {
       $this->shipmentRepository =  $shipmentRepository;
    }

    /**
     * Return Shipements LIST.
     *
     * @return array
    */

    public function index($limit, $page,$orderBy = null, $ascending = null, $user_id, $service = null, $waybill = null, $dateFrom = null, $dateTo = null, $status = null)
    {
        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;

        $this->shipmentRepository->shipement($this->fields);

        $this->shipmentRepository->leftJoin('customers as senders', 'senders.customer_id', 'shipments.sender_id');
        $this->shipmentRepository->leftJoin('customers as receivers', 'receivers.customer_id', 'shipments.receiver_id');

        $this->shipmentRepository->queryRelation('shipments.user_id', $user_id);

        $count = $this->shipmentRepository->count();
        $this->shipmentRepository->limit($limit, $page);

        if( $waybill != null ){
            $this->shipmentRepository->queryRelation('shipments.tracking_number', $waybill);
        }

        if( $service != null ){
            $this->shipmentRepository->queryRelation('shipments.service_name', $service);
        }

        if( $dateFrom != null && $dateTo != null ){
            $this->shipmentRepository->whereBetween('shipments.s_date', $dateFrom, $dateTo);
        }

        if( $status != null ){
            $this->shipmentRepository->queryRelation('shipments.status', $status);
        }

        if( $orderBy != null){
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $orderBy = 'shipments.'.$orderBy;
            $this->shipmentRepository->orderBy($orderBy,  $direction);
        }

        $results = $this->shipmentRepository->all();

        return [
            'data' => $results,
            'pagination' => [
                'numPage' => intval($page),
                'resultPage' => count($results),
                'totalResult' => $count
            ]
        ];
    }

    /**
     * Create an instance of Shipement
     * 
     * @return  App\Models\Shipement
    */

    public function storeShipement($data = [])
    {
        $data['service'] = json_encode($data['service']);
        $data['pieces'] = json_encode($data['pieces']);
        $data['shipment_response'] = json_encode($data['shipment_response']);

        return $this->shipmentRepository->createShipment($data);
    }

    /**
      * Return an especify Shipment
      *
      * @return  App\Models\Shipement
    */

    public function showShipement($id) : Shipment
    {   
        return $this->shipmentRepository->getShipement($id);
    }

    /**
     * Check shipment by user
     * 
     * @return bool;
    */

    public function checkShipmentByUser($id) : bool
    {   
        return count($this->shipmentRepository->checkHowManyShipmentByUser($id)) > 0;
    }
}