<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = getTokenSiigo($_ENV);

exit(json_encode(["token" => $token]));

function getTokenSiigo($env){

    if($env["mode"] === "production"){

        $result = apiSiigo('POST', '/connect/token', [
            'headers' => [
                'Content-Type'      => 'application/x-www-form-urlencoded',
                'Authorization'     => $env["authorization"],
                'Accept'            => 'application/json'
            ],
            'form_params' => [
                'grant_type'    => $env["production_grant_type"],
                'username'      => $env["production_username"],
                'password'      => $env["production_password"],
                'scope'         => $env["production_scope"],
            ]
        ], $env["siigo_server"]);

    }else{

        $result = apiSiigo('POST', '/connect/token', [
            'headers' => [
                'Content-Type'      => 'application/x-www-form-urlencoded',
                'Authorization'     => $env["authorization"],
                'Accept'            => 'application/json'
            ],
            'form_params' => [
                'grant_type'    => $env["sandbox_grant_type"],
                'username'      => $env["sandbox_username"],
                'password'      => $env["sandbox_password"],
                'scope'         => $env["sandbox_scope"],
            ]
        ], $env["siigo_server"]);
    }

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
