<?php
namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;

class CustomerService{

    /**
     *  The customerRepository for consuming the repository Customer
    */


    private $customerRepository;
    public $fields = [
        'customers.customer_id',
        'customers.user_id',
        'customers.name',
        'customers.contact_name',
        'customers.email',
        'customers.address1',
        'customers.address2',
        'customers.zip',
        'customers.phone',
        'customers.is_active',
        'customers.type_customer', // SENDER or RECEIVER
        'customers.extra',
        'cities.city_id',
        'cities.name as city_name',
        'provinces.province_id',
        'provinces.name AS province_name',
        'countries.country_id',
        'countries.name AS country_name',
    ];

    /**
     * Create a new service instance.
     *
     * @return void
    */

    public function __construct(CustomerRepository $customerRepository)
    {
       $this->customerRepository =  $customerRepository;
    }

     /**
     * Return Customers LIST.
     *
     * @return array
    */

    public function index($limit, $page, $query = null, $user_id = null, $country_id = null, $province_id = null, $city_id = null, $orderBy = null, $ascending = null)
    {
        $limit = $limit != null ? $limit : 30;
        $page = $page != null ? $page : 1;

        $this->customerRepository->customer($this->fields);

        $this->customerRepository->leftJoin('provinces', 'provinces.province_id', 'customers.province_id');
        $this->customerRepository->leftJoin('countries', 'countries.country_id', 'customers.country_id');
        $this->customerRepository->leftJoin('cities', 'cities.city_id', 'customers.city_id');

        if( $country_id != null ){
            $this->customerRepository->queryRelation('customers.country_id', $country_id);
        }

        if( $province_id != null ){
            $this->customerRepository->queryRelation('customers.province_id', $province_id);
        }

        if( $city_id != null ){
            $this->customerRepository->queryRelation('customers.city_id', $city_id);
        }

        if( $user_id != null ){
            $this->customerRepository->queryRelation('customers.user_id', $user_id);
        }

        if( $query != null ){
            $this->customerRepository->query($query);
        }

        $count = $this->customerRepository->count();
        $this->customerRepository->limit($limit, $page);

        if( $orderBy != null){
            $direction = $ascending == null || $ascending == 1 ? 'ASC' : 'DESC';
            $orderBy = 'customers.'.$orderBy;
            $this->customerRepository->orderBy($orderBy,  $direction);
        }

        $results = $this->customerRepository->all();

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
     * Return Customers All Search.
     *
     * @return array
    */

    public function searchCustomer($query = null, $user_id = null, $isSender = true)
    {
        $results = [];
        $this->customerRepository->customer($this->fields);
        $this->customerRepository->leftJoin('provinces', 'provinces.province_id', 'customers.province_id');
        $this->customerRepository->leftJoin('countries', 'countries.country_id', 'customers.country_id');
        $this->customerRepository->leftJoin('cities', 'cities.city_id', 'customers.city_id');


        if( $query != null ){
            $type_customer = $isSender ? 'SENDER' : 'RECEIVER';
            $this->customerRepository->queryRelation('customers.user_id', $user_id);
            $this->customerRepository->queryRelation('customers.type_customer', $type_customer);
           

            $this->customerRepository->query($query);
            $results = $this->customerRepository->all();
        }

        return ['data' => $results];
    }

    /**
     * Create an instance of Customer
     * 
     * @return  App\Models\Customer
    */


    public function storeCustomer($dataCustomer) : Customer
    {
        $dataCustomer['type_customer'] = $dataCustomer['isSender'] == 1 ? 'SENDER' : 'RECEIVER';
        unset($dataCustomer['isSender']);

        if( isset($dataCustomer['extra']) ){
            $dataCustomer['extra'] = json_encode($dataCustomer['extra']);
        }

        return $this->customerRepository->createCustomer($dataCustomer);
    }

    /**
     * Create an instace of Customer for sender or shipper
     * 
     * @return  App\Models\Customer
    */

    public function storCustomerForSenderOrReceiver($data) : Customer
    {
        if( isset($data['email']) && $this->isCheckEmail($data['email'], $data['user_id']) ){
            return $this->customerRepository->getCustomerForFieldAndUserId($data['user_id'], 'email', $data['email']);
        }

        if( $this->isCheckName($data['name'], $data['user_id']) ){
            return $this->customerRepository->getCustomerForFieldAndUserId($data['user_id'], 'name', $data['name']);
        }

        if( $this->isCheckContactName($data['contact_name'],$data['user_id']) ){
            return $this->customerRepository->getCustomerForFieldAndUserId($data['user_id'], 'contact_name', $data['contact_name']);
        }

        return $this->storeCustomer($data);
    }


     /**
      * Return an especify Customer
      *
      * @return  App\Models\Customer
    */

    public function showCustomer($id) : Customer
    {   
        return $this->customerRepository->getCustomer($id);
    }

     /**
      * Return bool in check email
      *
      * @return  bool
    */

    public function isCheckEmail($email, $userId) : bool
    {
        return $this->customerRepository->whereUserIdFromX($userId, 'email', $email);
    }

    
    /**
      * Return bool in check name
      *
      * @return  bool
    */

    public function isCheckName($name, $userId) : bool
    {
        return $this->customerRepository->whereUserIdFromX($userId, 'name', $name);
    }

    /**
      * Return bool in check contact name
      *
      * @return  bool
    */

    public function isCheckContactName($contact_name, $userId) : bool
    {
        return $this->customerRepository->whereUserIdFromX($userId, 'contact_name', $contact_name);
    }

}