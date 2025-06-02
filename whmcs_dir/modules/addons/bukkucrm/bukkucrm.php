<?php
// File: /modules/addons/bukkucrm/bukkucrm.php

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Bukkucrm\Admin\AdminDispatcher;
use WHMCS\Module\Addon\Bukkucrm\Client\ClientDispatcher;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function bukkucrm_config() {
    return [
        'name' => 'Bukku WHMCS CRM module',
        'description' => '',
        'author' => 'WGS',
        'language' => 'english',
        'version' => '1.0',
        'fields' => [
            'access_hash' => [
                'FriendlyName' => 'Access Token',
                'Type' => 'textarea',
                'Rows' => '3',
                'Cols' => '60',
                'Description' => 'Enter Access hash token here',
            ],
            'sub_domain' => [
                'FriendlyName' => 'Company Sub Domain',
                'Type' => 'text',
                'Size' => '25',
                'Description' => "Company's Sub Domain goes here",
            ],
            'api_test_connection' => [
                'FriendlyName' => 'Test Mode',
                'Type' => 'yesno',
                'Description' => "Enable this to activate test mode.",
            ],
        ]
    ];
}

function bukkucrm_activate() {
    try {
        if(Capsule::table('tblcustomfields')->where('fieldname','like','bukkuClientID|%')->count()==0){
            Capsule::table('tblcustomfields')->insert([
                'type'=>'client', 'relid'=>0, 'fieldname'=>'bukkuClientID|Bukku Client Id', 'fieldtype'=>'text', 'description'=>'', 'fieldoptions'=>'', 'regexpr'=>'', 'adminonly'=> '', 'required'=>'', 'showorder'=>'', 'showinvoice'=>'', 'sortorder'=>0,
            ]);
        }
        return [
            'status' => 'success',
            'description' => 'Module activated successfully',
        ];
    } catch (\Exception $e) {
        return [
            'status' => "error",
            'description' => 'Unable to activate module: ' . $e->getMessage(),
        ];
    }
}

function bukkucrm_deactivate() {
    try {
        return [
            'status' => 'success',
            'description' => 'Module deactivated successfully',
        ];
    } catch (\Exception $e) {
        return [
            "status" => "error",
            "description" => "Unable to drop : {$e->getMessage()}"
        ];
    }
}

function bukkucrm_output($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'clients';

    $dispatcher = new AdminDispatcher();
    $dispatcher->dispatch($action, $vars);
}