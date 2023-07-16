<?php

namespace App\Http\Controllers;

use App\Helpers\IsValidChange;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Services\CountryService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    use ApiResponse;
    private $countryService;

    /**
     * Create a new controller instance.
     *
     * @return void
    */
    
    public function __construct(CountryService $countryService)
    {
       $this->countryService = $countryService;
    }


    /**
     * Return Countries LIST.
     *
     * @return Illuminate\Http\Response
    */

    public function index(Request $request)
    {
       return $this->successResponse($this->countryService->index($request->limit, $request->page, $request->search, $request->orderBy, $request->ascending));
    }

    /**
     * Return Countries Search All.
     *
     * @return Illuminate\Http\Response
    */

    public function search(Request $request)
    {
        return $this->successResponse($this->countryService->searchContry($request->search));
    }

    /**
     * Create an instance of Country
     * 
     * @return  Illuminate\Http\Response
    */

    public function store(StoreCountryRequest $request)
    {
        return $this->successResponse(['data' => $this->countryService->storeCountry($request->all())]);
    }


    /**
      * Return an especify Country
      *
      * @return  Illuminate\Http\Response
    */

    public function show($id)
    {
        return $this->successResponse(['data' => $this->countryService->showCountry($id)]);
    }

    /**
      * Update the information of an existing Country
      *
      * @return  Illuminate\Http\Response
    */

    public function update($id, UpdateCountryRequest $request)
    {
        $country = $this->countryService->showCountry($id);

        if(  IsValidChange::compare($request->name, $country->name) && $this->countryService->isNameCountry($request->name) ){
            return $this->errorResponse('The name has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if(  IsValidChange::compare($request->code, $country->code) && $this->countryService->isCodeCountry($request->code) ){
            return $this->errorResponse('The code has already been taken.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $country->fill($request->all());

        if( $country->isClean() ){
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $country->update();

        return  $this->successResponse(['data' => $country]);
    }

    
    /**
      * Removes an existing Country
      *
      * @return  Illuminate\Http\Response
    */

    public function destroy($id)
    {
        $country = $this->countryService->showCountry($id);
        $country->delete();

        return $this->successResponse(['data' => $country]);
    }
}
