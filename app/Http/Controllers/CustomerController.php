<?php

namespace App\Http\Controllers;

use App\Helpers\getIdUserClient;
use App\Helpers\IsValidChange;
use App\Helpers\validJsonProperty;
use App\Http\Requests\SearchCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Services\CityService;
use App\Services\CountryService;
use App\Services\CustomerService;
use App\Services\ProvinceService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    use ApiResponse;
    private $userService;
    private $provinceService;
    private $countryService;
    private $cityService;
    private $customerService;

    /**
     * Create a new controller instance.
     *
     * @return void
    */

    public function __construct(UserService $userService, ProvinceService $provinceService, CountryService $countryService, CityService $cityService, CustomerService $customerService)
    {
       $this->userService =  $userService;
       $this->provinceService = $provinceService;
       $this->countryService = $countryService;
       $this->cityService = $cityService;
       $this->customerService = $customerService;
    }

    
    /**
     * Return Customers LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function index(Request $request)
    {
        $user = $this->userService->showUser($request->user_id);
        $user_id = getIdUserClient::getIdUser($user);

        return $this->successResponse($this->customerService->index($request->limit, $request->page, $request->search, $user_id, $request->country_id, $request->province_id, $request->city_id, $request->orderBy, $request->ascending));
    }

    /**
     * Return Customers Search All.
     *
     * @return Illuminate\Http\Response
    */

    public function search(SearchCustomerRequest $request)
    {
        $user = $this->userService->showUser($request->user_id);
        $user_id = getIdUserClient::getIdUser($user);

        return $this->successResponse($this->customerService->searchCustomer($request->search, $user_id, $request->isSender));
    }

    
    /**
     * Create an instance of Customer
     * 
     * @return  Illuminate\Http\Response
    */

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->data;
        $successResponse = [];

        for ($i=0; $i < count($data) ; $i++) { 
            $user = $this->userService->showUser($data[$i]['user_id']);

            $this->countryService->showCountry($data[$i]['country_id']);
            $this->cityService->showCity($data[$i]['city_id']);

            if (isset($data[$i]['province_id'])) {
                $this->provinceService->showPronvince($data[$i]['province_id']);
            }

            $data[$i]['user_id'] = getIdUserClient::getIdUser($user);

            if( isset($data[$i]['email']) && $this->customerService->isCheckEmail($data[$i]['email'], $data[$i]['user_id']) ){
                return $this->errorResponse('The email has already been taken. The position '.$i, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            if( $this->customerService->isCheckName($data[$i]['name'], $data[$i]['user_id']) ){
                return $this->errorResponse('The name has already been taken. The position '.$i, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            if( $this->customerService->isCheckContactName($data[$i]['contact_name'], $data[$i]['user_id']) ){
                return $this->errorResponse('The contact name has already been taken. The position '.$i, Response::HTTP_UNPROCESSABLE_ENTITY);
            }


        }

        for ($i=0; $i < count($data) ; $i++) { 
            array_push( $successResponse, $this->customerService->storeCustomer($data[$i]));
        }

        return $this->successResponse(['data' => $successResponse]);
    }

    /**
      * Return an especify Customer
      *
      * @return  Illuminate\Http\Response
    */

    public function show($id){
        return $this->successResponse(['data' => $this->customerService->showCustomer($id)]);
    }

    /**
      * Update the information of an existing Customer
      *
      * @return  Illuminate\Http\Response
    */

    public function update($id, UpdateCustomerRequest $request)
    {
        $customer = $this->customerService->showCustomer($id);
        $data = $request->all();

        if(  IsValidChange::compare($request->country_id, $customer->country_id)  ){
            $this->countryService->showCountry($request->country_id);
        }

        if(  IsValidChange::compare($request->province_id, $customer->province_id)  ){
            $this->provinceService->showPronvince($request->province_id);
        }

        if(  IsValidChange::compare($request->city_id, $customer->city_id)  ){
            $this->cityService->showCity($request->city_id);
        }

        if(  IsValidChange::compare($request->user_id, $customer->user_id)  ){
            $user = $this->userService->showUser($request->user_id);
            $data['user_id'] = getIdUserClient::getIdUser($user);
        }

        if( 
            IsValidChange::compare($request->email, $customer->email) && $this->customerService->isCheckEmail($request->email, $customer->user_id)
            || IsValidChange::compare($request->email, $customer->email) && IsValidChange::compare($request->user_id, $customer->user_id && $this->customerService->isCheckEmail($request->email, $request->user_id))
        ){
            return $this->errorResponse('The email has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if( isset($data['isSender']) ){
            $data['type_customer'] = $data['isSender'] == 1 ? 'SENDER' : 'RECEIVER';
            unset($data['isSender']);
        }
        
        if( isset($data['extra']) ){
            $data['extra'] = json_encode(json_decode($data['extra']));
        }

        $customer->fill($data);

        if( $customer->isClean() ){
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customer->update();

        return $this->successResponse(['data' => $customer]);
    }

    /**
      * Removes an existing Customer
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $customer = $this->customerService->showCustomer($id);

        $customer->delete();
        
        return $this->successResponse(['data' => $customer]); 
    }
}
