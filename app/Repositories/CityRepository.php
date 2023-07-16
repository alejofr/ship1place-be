<?php 

namespace App\Repositories;

use App\Models\City;

/**
 * For all the management to the database model, through Eloquent ORM. 
*/


class CityRepository{

    public $modelo;
    public $search;

    /**
     *  Instance for Model City
    */


    public function city($select = ['*'])
    {
        return $this->modelo = City::select($select);
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
     *  Method query ORM 
    */

    public function query($query)
    {
        $this->search = $query;

        $this->modelo = $this->modelo->Where(function($query) {
            $query->orWhere('cities.name',  'LIKE', '%'.$this->search.'%');
        });
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

    public function createCity($CityData): City
    {
        return City::create($CityData);
    }

    /**
     *  Method findOrFail ORM 
    */

    public function getCity($City) : City
    {
        return City::findOrFail($City);
    }

    /**
     *  Method is check field city compare value
    */

    public function isCheckValue($field, $value) : City | null
    {
        return City::where($field, '=', $value)->first();
    }
}