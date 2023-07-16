<?php

namespace App\Http\Controllers\DHL;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetRatesDHLRequest;
use App\Services\DHL\ServiceGetRates;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RateController extends Controller
{
    use ApiResponse;

    /**
     * DHL rates get
     * 
     * @return Illuminate\Http\Response
    */

    public function getRates(GetRatesDHLRequest $request)
    {
        $data = $request->all();
        $indicesNotExtras = ['shipperDetails', 'receiverDetails', 'plannedShippingDateAndTime', 'unitOfMeasurement', 'packages', 'isCustomsDeclarable'];
        $extras = [];

        foreach ($data as $key => $value) {
            if( !in_array($key, $indicesNotExtras) ){
                $extras[$key] = $data[$key];
            }
        }

        if(  $data['isCustomsDeclarable'] == false && isset($extras['monetaryAmount']) ){
            unset($extras['monetaryAmount']);
        }

        $getRates = new ServiceGetRates(
            [
                'shipperDetails' => $data['shipperDetails'],
                'receiverDetails' => $data['receiverDetails']
            ],
            $data['plannedShippingDateAndTime'],
            $data['unitOfMeasurement'],
            $data['packages'],
            $data['isCustomsDeclarable'],
            $extras
        );

        return $this->successResponse(['data'=> $getRates->getRates()]);
    }
}
