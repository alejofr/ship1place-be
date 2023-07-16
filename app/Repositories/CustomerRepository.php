<?php 

namespace App\Repositories;

use App\Models\Customer;

/**
 * For all the management to the database model, through Eloquent ORM. 
*/

class CustomerRepository{

    public $modelo;
    public $search;


    /**
     *  Instance for Model Customer
    */

    public function customer($select = ['*'])
    {
        return $this->modelo = Customer::select($select);
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
            $query->orWhere('customers.name',  'LIKE', '%'.$this->search.'%')
            ->orWhere('customers.contact_name',  'LIKE', '%'.$this->search.'%')
            ->orWhere('customers.email',  'LIKE', '%'.$this->search.'%');
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

    public function createCustomer($customerData): Customer
    {
        return Customer::create($customerData);
    }

    
    /**
     *  Method findOrFail ORM 
    */

    public function getCustomer($customer) : Customer
    {
        return Customer::findOrFail($customer);
    }

    /**
     * Method for get Customer for user_id and field
    */

    public function getCustomerForFieldAndUserId($userId, $field, $value) : Customer
    {
        return Customer::where('user_id', '=', $userId)->where($field, '=', $value)->first();
    }

    /**
     *  Method is check user_id from field Customer
    */

    public function whereUserIdFromX($userId, $field, $value) : bool
    {
        if( Customer::where('user_id', '=', $userId)->where($field, '=', $value)->count() > 0 ){
            return true;
        }

        return false;
    }


}