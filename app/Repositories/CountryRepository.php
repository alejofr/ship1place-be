<?php 

namespace App\Repositories;

use App\Models\Country;

/**
 * For all the management to the database model, through Eloquent ORM. 
*/

class CountryRepository{

    public $modelo;
    public $search;

    /**
     *  Instance for Model Country
    */

    public function country($select = ['*'])
    {
        return $this->modelo = Country::select($select);
    }

    /**
     *  Method query ORM 
    */

    public function query($query)
    {
        $this->search = $query;

        $this->modelo = $this->modelo->Where(function($query) {
            $query->orWhere('name',  'LIKE', '%'.$this->search.'%')
            ->orWhere('code',  'LIKE', '%'.$this->search.'%')
            ->orWhere('phone_code',  'LIKE', '%'.$this->search.'%');
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

    public function createCountry($CountryData): Country
    {
        return Country::create($CountryData);
    }

    /**
     *  Method findOrFail ORM 
    */

    public function getCountry($Country) : Country
    {
        return Country::findOrFail($Country);
    }

    /**
     *  Method is check field Country compare for value 
    */

    public function isCheckValue($field, $value) : Country | null
    {
        return Country::where($field, '=', $value)->first();
    }
}