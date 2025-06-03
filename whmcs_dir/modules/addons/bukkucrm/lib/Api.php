<?php

namespace WHMCS\Module\Addon\Bukkucrm;

require_once __DIR__ . '/../../../../init.php';
use WHMCS\Module\Addon\Bukkucrm\Helper;
use WHMCS\Database\Capsule;


class Api {

    function create_contact($data, $token) {
       
        $curl = new Curl();
        $curl->endPoint = '/contacts';
        $curl->header =  [
            'Accept: application/json',
            'Company-Subdomain: myinvoisdemo',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token->value,
        ];

        $curl->action = __FUNCTION__;
        $curl->method = 'POST';

        $curl->data = $data;

        $curlresponse = ($curl->curlCall());
        return $curlresponse;
    }



    function create_invoice($data, $token) {
        // echo "<pre>"; print_r($data); die;
       
        $curl = new Curl();
        $curl->endPoint = '/sales/invoices';
        $curl->header =  [
            'Accept: application/json',
            'Company-Subdomain: myinvoisdemo',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token->value,
        ];

        $curl->action = __FUNCTION__;
        $curl->method = 'POST';

        $curl->data = $data;

        $curlresponse = ($curl->curlCall());
        return $curlresponse;
    }

}
