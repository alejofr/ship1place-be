<?php

namespace App\Http\Controllers\Purolator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use SoapHeader;
use Exception;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
  /**
   * Creates a SOAP Client in Non-WSDL mode with the appropriate authentication and header information
   *
   * @return SoapClient
   */
  public function createPWSSOAPClient_Tracking()
  {
    $client = new SoapClient(
      resource_path() . "/purolator/TrackingService.wsdl",
      array(
        'trace'            =>    true,
        'location'    =>    "https://" . $_ENV['PUROLATOR_DOMAIN'] . "/PWS/V1/Tracking/TrackingService.asmx",
        'uri'                =>    "http://purolator.com/pws/datatypes/v1",
        'login'            =>    $_ENV['PUROLATOR_KEY_TEST'],
        'password'    =>    $_ENV['PUROLATOR_PASS_TEST'],
      )
    );
    //Define the SOAP Envelope Headers
    $headers[] = new SoapHeader(
      'http://purolator.com/pws/datatypes/v1',
      'RequestContext',
      array(
        'Version'           =>  '1.2',
        'Language'          =>  'en',
        'GroupID'           =>  'xxx',
        'RequestReference'  =>  'Request Tracking'
      )
    );
    //Apply the SOAP Header to your client
    $client->__setSoapHeaders($headers);

    return $client;
  }

  /**
   * Get Purolator Tracking info
   *
   * @param Illuminate\Http\Request  with Purolator shipment information
   * @return json waybill generated
   */
  public function requestTracking($waybill)
  {
    $result = array(
      "status" => 'ERROR',
      "message" => '',
      "data" => array()
    );

    $response = '';
    $responseStatus = 'SUCCESS';

    try {
      $client = $this->createPWSSOAPClient_Tracking();

      $infoToTrack = array(
        'PINs' => array(
          'PIN' => array(
            'Value' => $waybill
          )
        )
      );

      Log::info('Purolator Tracking request ' . json_encode($infoToTrack));

      $result = $client->TrackPackagesByPin($infoToTrack);

      Log::info('Purolator Tracking response ' . json_encode($result));

      if (property_exists($result->ResponseInformation, 'Errors') && property_exists($result->ResponseInformation->Errors, 'Error')) {
        //verify if there is an error and return quotes
        if (is_array($result->ResponseInformation->Errors->Error)) {
          for ($i = 0; $i < count($result->ResponseInformation->Errors->Error); $i++) {
            $response .= $result->ResponseInformation->Errors->Error[$i]->Code . ': ' . $result->ResponseInformation->Errors->Error[$i]->Description . '<br>';
          }
        } else {
          $response = $result->ResponseInformation->Errors->Error->Code . ': ' . $result->ResponseInformation->Errors->Error->Description;
        }
      } else {
        $response = $result->TrackingInformationList->TrackingInformation->Scans->Scan;
      }
    } catch (Exception $e) {
      $responseStatus = 'ERROR';
      $response = 'Purolator: ' . $e->getMessage();
    }
    Log::info('Purolator Tracking response ' . json_encode($response));

    return response([
      'status' => $responseStatus,
      'result' => json_encode($response)
    ], 200);
  }
}
