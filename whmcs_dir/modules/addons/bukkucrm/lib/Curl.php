<?php

namespace WHMCS\Module\Addon\Bukkucrm;
use WHMCS\Database\Capsule;

class Curl{
    private $baseUrl = 'https://api.bukku.fyi';
    public $token = '';
    private $key = '';
    public $method = 'GET';
    public $data = [];
    public $header = [];
    public $endPoint = '';
    public $action = '';
    public $curl = null;

    public function __construct(){  
        if (Capsule::table('tbladdonmodules')->where('setting', 'api_test_connection')->where('module', 'bukkucrm')->where('value', 'on')->first()) {
            $this->baseUrl = 'https://api.bukku.fyi'; //testing
        }else{
            // $this->baseUrl = 'https://api.bukku.my/'; // live
        }
    }

    /* Curl Handlig */
    function curlCall()
    {
        try {
            $this->curl = curl_init();
            switch ($this->method) {
                case 'POST':
                    curl_setopt($this->curl, CURLOPT_POST, 'POST');
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, (count($this->data) > 0 ? json_encode($this->data) : ""));
                    break;

                case 'PUT':
                    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, (count($this->data) > 0 ? json_encode($this->data) : ""));
                    break;

                case 'DELETE':
                    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, (count($this->data) > 0 ? json_encode($this->data) : ""));
                    break;

                case 'PATCH':
                    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, (count($this->data) > 0 ? json_encode($this->data) : ""));
                    break;

                default:
                    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');

            }

            curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl . $this->endPoint);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($this->curl, CURLOPT_MAXREDIRS, 10);
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($this->curl, CURLOPT_TIMEOUT, 10000); //timeout in seconds
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);

            $response = curl_exec($this->curl);

            $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

            logModuleCall("bukkucrm", $this->action, [
                "url" => $this->baseUrl . $this->endPoint,
                "method" => $this->method,
                "data" => $this->data
            ], [
                "httpCode" => $httpCode,
                "result" => json_decode($response),
            ]); 

            $response = ['status_code'=>$httpCode,'response'=>$response];
            return $response; 

            // if (curl_errno($this->curl)) {
            //     throw new \Exception(curl_error($this->curl));
            // }

            // return ['httpcode' => $httpCode, 'result' => json_decode($response)];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}