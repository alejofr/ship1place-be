<?php

namespace App\Http\Controllers;

use App\Helpers\IsValidChange;
use App\Http\Requests\StoreProvinceRequest;
use App\Http\Requests\UpdateProvinceRequest;
use App\Services\CountryService;
use App\Services\ProvinceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProvinceController extends Controller
{
    use ApiResponse;
    private $provinceService;
    private $countryService;

    /**
     * Create a new controller instance.
     *
     * @return void
    */

    public function __construct(ProvinceService $provinceService, CountryService $countryService)
    {
       $this->provinceService = $provinceService;
       $this->countryService = $countryService;
    }

    /**
     * Return Pronvinces LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function index(Request $request)
    {
       return $this->successResponse($this->provinceService->index($request->limit, $request->page, $request->search, $request->orderBy, $request->ascending));
    }

    /**
     * Return Pronvinces Search All.
     *
     * @return Illuminate\Http\Response
    */

    public function search(Request $request)
    {
        return $this->successResponse($this->provinceService->searchProvince($request->search, $request->country_id));
    }

    /**
     * Create an instance of Pronvince
     * 
     * @return  Illuminate\Http\Response
    */

    public function store(StoreProvinceRequest $request)
    {
        $this->countryService->showCountry($request->country_id);
        
        return $this->successResponse(['data' => $this->provinceService->storeProvince($request->all())]);
    }

    /**
      * Return an especify Pronvice
      *
      * @return  Illuminate\Http\Response
    */

    public function show($id)
    {
        return $this->successResponse(['data' => $this->provinceService->showPronvince($id)]);
    }

    /**
      * Update the information of an existing Province
      *
      * @return  Illuminate\Http\Response
    */

    public function update($id, UpdateProvinceRequest $request)
    {
        $province = $this->provinceService->showPronvince($id);

        if(  IsValidChange::compare($request->country_id, $province->country_id)  ){
            $this->countryService->showCountry($request->country_id);
        }

        if(  IsValidChange::compare($request->name, $province->name) && $this->provinceService->isNameProvince($request->name) ){
            return $this->errorResponse('The name has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(  IsValidChange::compare($request->code, $province->code) && $this->provinceService->isCodeProvince($request->code) ){
            return $this->errorResponse('The code has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $province->fill($request->all());

        if( $province->isClean() ){
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $province->update();

        return $this->successResponse(['data'=>$province]);
    }

    /**
      * Removes an existing Province
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $province = $this->provinceService->showPronvince($id);
        $province->delete();

        return $this->successResponse(['data'=>$province]);
    }
}
