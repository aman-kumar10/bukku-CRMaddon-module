<?php

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Bukkucrm\Helper;

// Synchronization on client add hook
add_hook('ClientAdd', 1, function(array $params) {
    try {
        $helper = new Helper;

        if($params['client_id']) {
            $helper->create_contact($params['client_id']); 
        }
        
    } catch (Exception $e) {
        // Consider logging or reporting the error.
        logActivity("Unable to sync client. Error: " . $e->getMessage());
    }
});



/* Synchronization on invoice creation hook */
add_hook('InvoiceCreation', 1, function ($vars) {
    try {
        $helper = new Helper;
        $invoiceId = $vars['invoiceid'];

        if ($invoiceId) {
            $invoice = Capsule::table('tblinvoices')->where('id', $invoiceId)->first();

            // Sync client linked to this invoice if not already
            $field_id = Capsule::table('tblcustomfields')->where('fieldname', 'like', 'bukkuClientID|%')->where('type', 'client')->value('id');
            $user_sync = Capsule::table('tblcustomfieldsvalues')->where('fieldid', $field_id)->where('relid', $invoice->userid)->value('value');

            if (!$user_sync) {
                $helper->create_contact($invoice->userid);
            }

            // Sync product linked to this invoice if not already
            $hostingItem = Capsule::table('tblinvoiceitems')->where('invoiceid', $invoiceId)->where('type', 'Hosting')->first();

            if ($hostingItem) {
                $hosting = Capsule::table('tblhosting')->where('id', $hostingItem->relid)->first();

                if ($hosting) {
                    $productId = $hosting->packageid;

                    $sync_product = Capsule::table('mod_synced_products')
                        ->where('pid', $productId)
                        ->first();

                    if (!$sync_product) {
                        $helper->create_product($productId);
                    }
                }
            }

            // Sync Invoice
            $helper->create_invoice($invoiceId);
        }
    } catch (Exception $e) {
        logActivity("Error in sync process, during invoice creation hook: " . $e->getMessage());
    }
});

