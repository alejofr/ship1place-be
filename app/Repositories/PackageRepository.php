<?php 

namespace App\Repositories;

use App\Models\Package;

/**
 * For all the management to the database model, through Eloquent ORM. 
*/

class PackageRepository{

    public $modelo;
    public $search;

     /**
     *  Instance for Model Package 
    */

    public function package($select = ['*'])
    {
        return $this->modelo = Package::select($select);
    }

    /**
     *  Method query ORM 
    */

    public function query($query)
    {
        $this->search = $query;

        $this->modelo = $this->modelo->Where(function($query) {
            $query->orWhere('name',  'LIKE', '%'.$this->search.'%');
        });
    }

    /**
     *  Method queryRelation ORM 
    */

    public function queryRelation($field, $value)
    {
        $this->modelo->where($field, '=', $value);
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
     *  Method findOrFail ORM 
    */

    public function getPackage($package) : Package
    {
        return Package::findOrFail($package);
    }


    /**
     *  Method create ORM 
    */

    public function createPackage($packageData): Package
    {
        return Package::create($packageData);
    }

    /**
     *  Method is check user_id and name User 
    */

    public function isCheckName($name, $userId) : bool
    {
        if( Package::where('user_id', '=', $userId)->where('name', '=', $name)->count() > 0 ){
            return true;
        }

        return false;
    }
}