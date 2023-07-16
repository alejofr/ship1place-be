<?php 

namespace App\Repositories;

use App\Models\Province;

/**
 * For all the management to the database model, through Eloquent ORM. 
*/

class ProvinceRepository{

    public $modelo;
    public $search;

    /**
     *  Instance for Model Province 
    */


    public function province($select = ['*'])
    {
        return $this->modelo = Province::select($select);
    }

    /**
     *  Method leftJoin ORM, relation in Country
    */

    public function leftJoinCountry()
    {
        $this->modelo->leftJoin('countries', 'countries.country_id', 'provinces.country_id');
    }

    
    /**
     *  Method queryRelation ORM 
    */

    public function queryIdCountry($value)
    {
        $this->modelo->where('countries.country_id', '=', $value);
    }

    
    /**
     *  Method query ORM 
    */

    public function query($query)
    {
        $this->search = $query;

        $this->modelo = $this->modelo->Where(function($query) {
            $query->orWhere('provinces.name',  'LIKE', '%'.$this->search.'%')
            ->orWhere('provinces.name',  'LIKE', '%'.$this->search.'%');
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

    public function createProvince($ProvinceData): Province
    {
        return Province::create($ProvinceData);
    }

    /**
     *  Method findOrFail ORM 
    */

    public function getProvince($Province) : Province
    {
        return Province::findOrFail($Province);
    }

    /**
     *  Method for compare field the Province with value
    */

    public function isCheckValue($field, $value) : Province | null
    {
        return Province::where($field, '=', $value)->first();
    }

    /**
     *  Method for compare country_id and province_id
    */

    public function compareIdCountryAndIdProvince($country_id, $province_id) : Province | null
    {
        return Province::where('country_id', '=', $country_id)->where('province_id', '=', $province_id)->first();
    }
}