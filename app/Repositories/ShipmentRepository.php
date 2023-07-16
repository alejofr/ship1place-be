<?php

namespace App\Repositories;

use App\Models\Shipment;

/**
 * For all the management to the database model, through Eloquent ORM. 
*/

class ShipmentRepository{

    public $modelo;

    /**
     *  Instance for Model Shipmement 
    */

    public function shipement($select = ['*'])
    {
        return $this->modelo = Shipment::select($select);
    }

    /**
     *  Method leftJoin ORM 
    */

    public function leftJoin($nameTable, $id, $idCompare)
    {
        $this->modelo->leftJoin($nameTable, $id, $idCompare);
    }

    /**
     *  Method queryRelation ORM 
    */

    public function queryRelation($field, $value)
    {
        $this->modelo->where($field, '=', $value);
    }

    /**
     *  Method whereBetween ORM 
    */

    public function whereBetween($field, $dataFrom, $dataTo)
    {
        $this->modelo->whereBetween($field, [$dataFrom, $dataTo]);
    }

    /**
     *  Method limit ORM 
    */

    public function limit($limit = 30, $page = 1)
    {
        $this->modelo->limit($limit)
        ->skip($limit * ($page - 1));
    }

    /**
     *  Method count ORM 
    */

    public function count() : int
    {
       return $this->modelo->count();
    }

    /**
     *  Method orderBy ORM 
    */

    public function orderBy($field, $ascending = 'ASC' ) // ASC or DESC
    {
        return $this->modelo->orderBy($field, $ascending);
    }

    /**
     *  Method all ORM 
    */

    public function all()
    {
       return $this->modelo->get()->toArray();
    }

    /**
     *  Method create ORM 
    */

    public function createShipment($ShipmentData): Shipment
    {
        return Shipment::create($ShipmentData);
    }

    /**
     *  Method findOrFail ORM 
    */

    public function getShipement($shipement) : Shipment
    {
        return Shipment::findOrFail($shipement);
    }

    /**
     * Method check how many shipments by user
     * 
     * @return array
    */

    public function checkHowManyShipmentByUser($user_id) : array 
    {
        return Shipment::where('user_id', '=', $user_id)->get()->toArray();
    }
    

}