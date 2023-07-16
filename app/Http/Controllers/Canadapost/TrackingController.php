<?php

namespace App\Http\Controllers\Canadapost;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use SOAPHeader;
use stdClass;
use SoapVar;
use Illuminate\Support\Facades\Log;
use Exception;

class TrackingController extends Controller
{
  /**
   * Create client request to special adapt canadapost request, it's a condition to modify xml request
   *
   * @return SoapClient
   */
  public function getClient()
  {
    $wsdl = resource_path() . '/canadapost/track.wsdl';

    $location = 'https://' . $_ENV['CANADA_POST_URL'] . '/vis/soap/track';
    $opts = array(
      'ssl' =>
      array(
        'verify_peer' => false,
        'cafile' => resource_path() . '/canadapost/cert/cacert.pem',
        'CN_match' => $_ENV['CANADA_POST_URL']
      ),
    );
    $ctx = stream_context_create($opts);
    $client = new SoapClient($wsdl, array('location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $ctx));

    // Set WS Security UsernameToken
    $WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    $usernameToken = new stdClass();
    $usernameToken->Username = new SoapVar($_ENV['CANADA_POST_USER'], XSD_STRING, null, null, null, $WSSENS);
    $usernameToken->Password = new SoapVar($_ENV['CANADA_POST_PWD'], XSD_STRING, null, null, null, $WSSENS);
    $content = new stdClass();
    $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $WSSENS);
    $header = new SOAPHeader($WSSENS, 'Security', $content);
    $client->__setSoapHeaders($header);

    return $client;
  }

  /**
   * Get tracking for Canada Post
   *
   * @param request
   * @return json
   */
  public function requestTracking($waybill)
  {
    $result = array(
      "status" => 'ERROR',
      "message" => '',
      "data" => array()
    );

    $responseStatus = 'SUCCESS';
    $responseResults = '';

    try {
      $client = $this->getClient();

      $sample = array(
        'get-tracking-detail-request' => array(
          'locale'    => 'EN',
          'pin'        => $waybill
        )
      );

      Log::info('Canadapost Tracking request ' . json_encode($sample));

      $response = $client->__soapCall('GetTrackingDetail', $sample, NULL, NULL);

      Log::info('Canadapost Tracking response ' . json_encode($response));

      if (isset($response->{'tracking-detail'})) {
        $responseResults = $response;
      } else {
        $error = '';
        foreach ($response->{'messages'}->{'message'} as $message) {
          $error = $error . ' ' . $message->description . ' ';
        }
        $responseStatus = 'ERROR';
        $responseResults = $error;
      }
    } catch (Exception $e) {
      Log::info('Canadapost Tracking error: ' . $e->getMessage());

      $responseResults = 'CanadaPost: ' . $e->getMessage();
    }
    return response([
      'status' => $responseStatus,
      'result' => json_encode($responseResults)
    ], 200);
  }
}
