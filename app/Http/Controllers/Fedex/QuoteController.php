<?php

namespace App\Http\Controllers\Fedex;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class QuoteController extends Controller
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
   * Generate FEDEX quote request
   *
   * @param  $shipment info
   *
   * @return \Illuminate\Http\Response
   */
  public function requestQuote($shipment)
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
          "accountNumber":
            {
              "value": ' . env('FEDEX_ACCOUNT', null) . '"
              }
            },
          "rateRequestControlParameters": {
            "returnTransitTimes": true,
            "servicesNeededOnRateFailure": true,
            "variableOptions": "FREIGHT_GUARANTEE",
            "rateSortOrder": "COMMITASCENDING"
          },
          "requestedShipment": {
            "shipper": {
              "address": {
                "streetLines":"' . $shipment->sender->account->address . '",
              },
              "city": "' . $shipment->sender->account->city_id->name . '",
              "stateOrProvinceCode": "' . $shipment->sender->account->province_id->name . '",
              "postalCode": "' . $shipment->sender->account->postal_code . '",
              "countryCode": "' . $shipment->sender->account->country_id->name . '",
              "residential": "' . $shipment->sender->account->residential . '"
            },
            "recipient": {
              "address": {
                "streetLines":"' . $shipment->receiver->account->address . '",
              },
              "city": "' . $shipment->receiver->account->city_id->name . '",
              "stateOrProvinceCode": "' . $shipment->receiver->account->province_id->name . '",
              "postalCode": "' . $shipment->receiver->account->postal_code . '",
              "countryCode": "' . $shipment->receiver->account->country_id->name . '",
              "residential": "' . $shipment->receiver->account->residential . '"
            },
            "emailNotificationDetail": {
              "recipients": [
                {
                  "emailAddress": "' . $shipment->receiver->account->email . '",
                }
              ]
            },
            "preferredCurrency": "CAD",
            "rateRequestType": "ACCOUNT",
            "shipDateStamp": "' . $shipment->ship_date . '",
            "pickupType": "DROPOFF_AT_FEDEX_LOCATION",
            "requestedPackageLineItems":
          }
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
