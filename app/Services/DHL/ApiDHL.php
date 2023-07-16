<?php

namespace App\Services\DHL;

use Illuminate\Support\Facades\Http;

class ApiDHL{
    public $client;
    public $url;

    public function __construct($url)
    {
        $baseUrl = env('APP_ENV') == 'production' ? env('PROD_DHL') : env('TEST_DHL');
        $this->client = Http::withBasicAuth( env('USERNAME_DHL'), env('PASSWORD_DHL') )->accept('application/json');
        $this->url = $baseUrl.'/'.$url;
    }

    public function delete($data = [])
    {
        $res = $this->client->delete($this->url, $data);

        return $res->throw()->json();
    }

    public function post($data = [])
    {
        $res = $this->client->post($this->url, $data);

        return $res->throw()->json();
    }

    public function get($data = [])
    {
        $res = $this->client->get($this->url, $data);

        return $res->json();
    }
}