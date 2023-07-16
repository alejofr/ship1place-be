<?php

namespace App\Http\Controllers\DHL;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetTrackingDHLRequest;
use App\Services\DHL\ServiceTracking;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    use ApiResponse;
    private $trackingDHLService;

    public function __construct(ServiceTracking $serviceTracking)
    {
        $this->trackingDHLService = $serviceTracking;
    }

    public function show($id, GetTrackingDHLRequest $request)
    {
        $data = $this->trackingDHLService->getTracking($id, $request->all());

        return $this->successResponse(['data'=> $data]);
    }
}
