<?php

namespace App\Http\Controllers\Fedex;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use SoapClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class TrackingController extends Controller
{
  private function getAuthToken()
  {
    try {
      $response = Http::asForm()->post(env('FEDEX_OAUTH'), [
        'grant_type' => 'client_credentials',
        'client_id' => env(
          'FEDEX_KEY',
          null
        ),
        'client_secret' => env('FEDEX_PWD', null),
      ]);

      if ($response->successful()) {
        $authToken = $response->throw()->json();
        return $authToken['access_token'];
      } else {
        return null;
      }
    } catch (Exception $ex) {
      return null;
    }
  }

  /**
   * Generate FEDEX tracking request
   *
   * @param  $waybill
   *
   * @return \Illuminate\Http\Response
   */
  public function requestTracking($waybill)
  {
    $authToken = $this->getAuthToken();
    if ($authToken == null) {
      return response([
        'status' => 'ERROR',
        'result' => 'FedEx: Error getting auth token'
      ], 200);
    } else {
      try {
        $body = '{
          "trackingInfo": [
            {
              "trackingNumberInfo": {
                "trackingNumber": "' . $waybill . '"
              }
            }
          ],
          "includeDetailedScans": true
        }';
        $response = Http::withHeaders([
          'X-locale' => 'en_US',
        ])->withToken($authToken)->withBody($body)->post(env('FEDEX_TRACKING_URL'));

        if ($response->successful()) {
          $trackingInfo = $response->throw()->json();
          return response([
            'status' => 'SUCCESS',
            'result' => json_encode($trackingInfo)
          ], 200);
        } else {
          return response(
            [
              'status' => 'ERROR',
              'result' => ''
            ],
            200
          );
        }
      } catch (Exception $ex) {
        return response([
          'status' => 'ERROR',
          'result' => json_encode($ex)
        ], 200);
      }
    }
  }
}
