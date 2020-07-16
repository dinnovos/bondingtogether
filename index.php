<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

$token = getTokenSiigo();

exit(json_encode(["token" => $token]));

function getTokenSiigo(){

    $result = apiSiigo('POST', '/connect/token', [
        'headers' => [
            'Content-Type'      => 'application/x-www-form-urlencoded',
            'Authorization'     => 'Basic U2lpZ29XZWI6QUJBMDhCNkEtQjU2Qy00MEE1LTkwQ0YtN0MxRTU0ODkxQjYx',
            'Accept'            => 'application/json'
        ],
        'form_params' => [
            'grant_type'    => 'password',
            'username'      => 'EMPRESA2CAPACITACION\empresa2@apionmicrosoft.com',
            'password'      => 's112pempresa2#',
            'scope'         => 'WebApi offline_access',
        ]
    ], 'https://siigonube.siigo.com:50050');

    if($result){
        $data = json_decode($result);

        if(isset($data->access_token)){
            $token = $data->access_token;
			
            return $token;
        }                
    }

    return null;
}

function apiSiigo($method, $url, $paramenters, $new_base_url = null){

    $base_url = "http://siigoapi.azure-api.net";
    $_url = "/siigo/api/v1{$url}";

    if($new_base_url){
        $base_url = $new_base_url;
        $_url = $url;
    }

    $client = new Client([
        'base_uri' => $base_url
    ]);

    try {
        $response = $client->request($method, $_url, $paramenters);
        $body = $response->getBody();
        return $body->getContents();
    } catch (\Exception $e) {}

    return null;
}
