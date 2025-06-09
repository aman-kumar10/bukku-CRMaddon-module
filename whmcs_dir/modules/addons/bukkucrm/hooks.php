<?php

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Bukkucrm\Helper;



// Sync client on new client create
add_hook('ClientAdd', 1, function(array $params) {

    $helper = new Helper;

    try {
        if($params['client_id']) {
            $create_contact = $helper->create_contact($params['client_id']); 
            logModuleCall( 'bukkucrm', "Create Contact", 'Contact create on new client add, Client Id: '. $params['client_id'], $create_contact);
        }
        
        
    } catch (Exception $e) {
        logActivity("Unable to sync client. Error: " . $e->getMessage());
    }
});