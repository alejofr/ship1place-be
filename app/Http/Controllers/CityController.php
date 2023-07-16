<?php

namespace App\Http\Controllers;

use App\Helpers\IsValidChange;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Services\CityService;
use App\Services\CountryService;
use App\Services\ProvinceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CityController extends Controller
{
    use ApiResponse;
    private $provinceService;
    private $countryService;
    private $cityService;

    /**
     * Create a new controller instance.
     *
     * @return void
    */


    public function __construct(ProvinceService $provinceService, CountryService $countryService, CityService $cityService)
    {
       $this->provinceService = $provinceService;
       $this->countryService = $countryService;
       $this->cityService = $cityService;
    }

     /**
     * Return Cities LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function index(Request $request)
    {
       return $this->successResponse($this->cityService->index($request->limit, $request->page, $request->search, $request->orderBy, $request->ascending));
    }

    /**
     * Return Cities Search All.
     *
     * @return Illuminate\Http\Response
    */

    public function search(Request $request)
    {
        return $this->successResponse($this->cityService->searchCity($request->search, $request->country_id, $request->province_id));
    }

     /**
     * Create an instance of City
     * 
     * @return  Illuminate\Http\Response
    */

    public function store(StoreCityRequest $request)
    {
        $this->countryService->showCountry($request->country_id);
        $this->provinceService->showPronvince($request->province_id);

        if( !$this->compareProvinceAndCountry($request->country_id, $request->province_id) ){
            return $this->errorResponse('You cannot add a country id different from province', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        return $this->successResponse(['data' => $this->cityService->storeCity($request->all())]);
    }

    /**
      * Return an especify City
      *
      * @return  Illuminate\Http\Response
    */

    public function show($id)
    {
        return $this->successResponse(['data' => $this->cityService->showCity($id)]);
    }

    /**
      * Update the information of an existing City
      *
      * @return  Illuminate\Http\Response
    */

    public function update($id, UpdateCityRequest $request)
    {
        $city = $this->cityService->showCity($id);

        if(  IsValidChange::compare($request->country_id, $city->country_id)  ){
            $this->countryService->showCountry($request->country_id);

            if( !$this->compareProvinceAndCountry($request->country_id, $request->province_id != null ? $request->province_id : $city->province_id) ){
                return $this->errorResponse('You cannot add a country id different from province', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if(  IsValidChange::compare($request->province_id, $city->province_id)  ){
            $this->provinceService->showPronvince($request->province_id);

            if( !$this->compareProvinceAndCountry($request->country_id != null ? $request->country_id : $city->country_id, $request->province_id) ){
                return $this->errorResponse('You cannot add a country id different from province', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if(  IsValidChange::compare($request->name, $city->name) && $this->cityService->isNameCity($request->name) ){
            return $this->errorResponse('The name has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $city->fill($request->all());

        if( $city->isClean() ){
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $city->update();

        return $this->successResponse(['data' => $city]);
    }

    /**
      * Check compare country and province id 
      *
      * @return bool
    */

    private function compareProvinceAndCountry($country_id, $province_id)
    {
        return $this->provinceService->checkIdCountryAndIdProvince($country_id, $province_id);
    }

    /**
      * Removes an existing City
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $city = $this->cityService->showCity($id);
        $city->delete();

        return $this->successResponse(['data' => $city]);
    }
}
