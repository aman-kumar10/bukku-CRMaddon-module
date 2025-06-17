<?php

/**
 * Bukku CRM WHMCS Addon Module
 * Handles synchronization of clients, products, and invoices with Bukku CRM.
 * Author: WGS
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Bukkucrm\Admin\AdminDispatcher;
use WHMCS\Module\Addon\Bukkucrm\Client\ClientDispatcher;
use WHMCS\Module\Addon\Bukkucrm\Helper;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/* 
 * Define module configuration options 
 */
function bukkucrm_config() {
    $healper = new Helper;
    
    $getAccounts = $healper->getAccounts();

    if($getAccounts['status_code'] == 200) {
        $getAccounts['response'] = json_decode($getAccounts['response'], true);
        $accounts = $getAccounts['response']['accounts'];
        foreach ($accounts as $account) {
            $accountOptions[$account['id']] = $account['name'] . " (" . $account['code'] . ")";

            if (!empty($account['children']) && is_array($account['children'])) {
                foreach ($account['children'] as $child) {
                    $accountOptions[$child['id']] = '-- ' . $child['name'] . " (" . $child['code'] . ")";
                }
            }
        }
    } else {
        $accounts = [];
    }

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
            'contact_type' => [
                'FriendlyName' => 'Contact Type',
                'Type' => 'dropdown',
                'Options' => [
                    'customer' => 'Customer',
                    'supplier' => 'Supplier',
                    'employee' => 'Employee',
                ],
                'Description' => 'Choose Contact Type',
            ],
            'select_account' => [
                'FriendlyName' => 'Account',
                'Type' => 'dropdown',
                'Options' => $accountOptions,
                'Description' => "Choose sales account",
            ],
            'api_test_connection' => [
                'FriendlyName' => 'Test Mode',
                'Type' => 'yesno',
                'Description' => "Enable this to activate test mode.",
            ],
        ]
    ];
}

/* 
 * Module Activation
 * - Creates custom client fields
 * - Creates custom tables for synced products and invoices
 */
function bukkucrm_activate() {
    try {
        // Create custom client field for storing Bukku Client ID
        if(Capsule::table('tblcustomfields')->where('fieldname','like','bukkuClientID|%')->count()==0){
            Capsule::table('tblcustomfields')->insert([
                'type'=>'client', 'relid'=>0, 'fieldname'=>'bukkuClientID|Bukku Client Id', 'fieldtype'=>'text', 'description'=>'', 'fieldoptions'=>'', 'regexpr'=>'', 'adminonly'=> '', 'required'=>'', 'showorder'=>'', 'showinvoice'=>'', 'sortorder'=>0,
            ]);
        }

        // Create table to store synced products group
        if (!Capsule::schema()->hasTable('mod_synced_productgroups')) {
            Capsule::schema()->create('mod_synced_productgroups', function ($table) {
                $table->increments('id');
                $table->text('name');
                $table->string('gid');
                $table->string('sync_gid');
                $table->timestamps();
            });
        }
        
        // Create table to store synced products
        if (!Capsule::schema()->hasTable('mod_synced_products')) {
            Capsule::schema()->create('mod_synced_products', function ($table) {
                $table->increments('id');
                $table->integer('pid');
                $table->integer('gid');
                $table->string('name');
                $table->integer('sync_pid');
                $table->integer('sync_gid');
            });
        }

        // Create table to store synced invoices
        if (!Capsule::schema()->hasTable('mod_synced_invoices')) {
            Capsule::schema()->create('mod_synced_invoices', function ($table) {
                $table->increments('id');
                $table->integer('invoice_id');
                $table->integer('product_id');
                $table->string('user_id');
                $table->string('contact_id');
                $table->integer('sync_invoiceID');
                $table->integer('sync_productID');
            });
        }

        // Create table for custom log activities
        if (!Capsule::schema()->hasTable('mod_bukkucrm_logs')) {
            Capsule::schema()->create('mod_bukkucrm_logs', function ($table) {
                $table->increments('id');
                $table->string('action');
                $table->longText('request');
                $table->string('http_code');
                $table->longText('response');
                $table->timestamp('datetime')->useCurrent();
            });
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

/*
 * Module Deactivation
 * - Currently does not drop any tables or fields
 */
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

/*
 * Module Admin Output Dispatcher
 * Routes admin actions through AdminDispatcher class
 */
function bukkucrm_output($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'clients';

    $dispatcher = new AdminDispatcher();
    $dispatcher->dispatch($action, $vars);
}