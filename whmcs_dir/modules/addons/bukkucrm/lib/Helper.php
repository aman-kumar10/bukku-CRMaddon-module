<?php

namespace WHMCS\Module\Addon\Bukkucrm;

use Exception;
use WHMCS\Module\Addon\Bukkucrm\Api;

use WHMCS\Database\Capsule;

require_once __DIR__ . '/../../../../init.php';


class Helper
{

    /* get clients **/
    function getClientsDataTable($start, $length, $search = '')
    {
        $field = Capsule::table('tblcustomfields')->where('type', 'client')->where('fieldname', 'like', 'bukkuClientID|%')->first();

        if (!$field) {
            return [];
        }

        $field_id = $field->id;

        $query = Capsule::table('tblclients')
            ->select('id', Capsule::raw("CONCAT(firstname, ' ', lastname) AS name"), 'email', 'companyname', 'status');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('companyname', 'like', "%$search%");
            });
        }

        $clients = $query->skip($start)->take($length)->get();

        $data = [];

        foreach ($clients as $client) {

            $field_id = Capsule::table('tblcustomfields')->where('fieldname', 'like', 'bukkuClientID|%')->where('type', 'client')->value('id');
            $customFieldIdForClient = Capsule::table('tblcustomfieldsvalues')->where('fieldid', $field_id)->where('relid', $client->id)->value('value');
            
            // Check if user already sync 
            if (!$customFieldIdForClient) {
                $data[] = [
                    'check_box' => '<input type="checkbox" name="selectedclients[]" value="' . $client->id . '" class="checkall">',
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'companyname' => $client->companyname,
                    'status' => $client->status,
                    'action_btns' => '
                    <a class="btn btn-primary btn-sm user-syn-btn" data-userid="' . $client->id . '" data-username="' . $client->name . '">
                        <i class="fas fa-sync" id="sync-icon-' . $client->id . '"></i> Sync
                    </a>'
                ];
            }
        }

        return $data;
    }
    public function getClientsCount($search = '')
    {
        $fieldId = Capsule::table('tblcustomfields')
            ->where('type', 'client')
            ->where('fieldname', 'like', 'bukkuClientID|%')
            ->value('id');

        if (!$fieldId) {
            return 0;
        }

        $query = Capsule::table('tblclients')
            ->leftJoin('tblcustomfieldsvalues', function ($join) use ($fieldId) {
                $join->on('tblclients.id', '=', 'tblcustomfieldsvalues.relid')
                    ->where('tblcustomfieldsvalues.fieldid', '=', $fieldId);
            })
            ->where(function ($q) {
                $q->whereNull('tblcustomfieldsvalues.value')
                ->orWhere('tblcustomfieldsvalues.value', '=', '');
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tblclients.firstname', 'like', "%$search%")
                ->orWhere('tblclients.lastname', 'like', "%$search%")
                ->orWhere('tblclients.email', 'like', "%$search%")
                ->orWhere('tblclients.companyname', 'like', "%$search%");
            });
        }

        return $query->count();
    }



    /* Get invoices */ 
    public function getInvoiceDataTable($start, $length, $search = '')
    {
        $query = Capsule::table('tblinvoices')
            ->join('tblclients', 'tblclients.id', '=', 'tblinvoices.userid')
            ->select(
                'tblinvoices.id',
                'tblinvoices.userid',
                'tblinvoices.invoicenum',
                'tblinvoices.date',
                'tblinvoices.total',
                'tblinvoices.status',
                'tblclients.firstname',
                'tblclients.lastname'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tblinvoices.userid', 'like', "%$search%")
                    ->orWhere('tblinvoices.invoicenum', 'like', "%$search%")
                    ->orWhere('tblinvoices.date', 'like', "%$search%")
                    ->orWhere('tblinvoices.total', 'like', "%$search%")
                    ->orWhere('tblinvoices.status', 'like', "%$search%")
                    ->orWhere('tblclients.firstname', 'like', "%$search%")
                    ->orWhere('tblclients.lastname', 'like', "%$search%");
            });
        }

        $invoices = $query->skip($start)->take($length)->get();


        $data = [];
        foreach ($invoices as $invoice) {
            $sync_invoice = Capsule::table('mod_synced_invoices')->where('invoice_id', $invoice->id)->first();

            // Check if Invoice already sync 
            if(!$sync_invoice) {
                $clientname = $invoice->firstname . " " . $invoice->lastname;
                $currency = getCurrency($invoice->userid);
    
                $data[] = [
                    'check_box' => '<input type="checkbox" name="selectedinvoices[]" value="' . $invoice->id . '" class="checkall">',
                    'id' => $invoice->invoicenum,
                    'client_name' => $clientname,
                    'invoice_date' => $invoice->date,
                    'total_amount' => $currency['prefix'] . $invoice->total . $currency['suffix'],
                    'status' => $invoice->status,
                    'action_btns' => '
                        <a class="btn btn-primary btn-sm invoice-syn-btn" data-invoiceid="' . $invoice->id . '"><i class="fas fa-sync" id="sync-icon-' . $invoice->id . '"></i> Sync</a>'
                ];
            }

        }

        return $data;
    }
    public function getInvoiceCount($search = '')
    {
        $query = Capsule::table('tblinvoices')
            ->leftJoin('mod_synced_invoices', 'tblinvoices.id', '=', 'mod_synced_invoices.invoice_id')
            ->join('tblclients', 'tblclients.id', '=', 'tblinvoices.userid')
            ->whereNull('mod_synced_invoices.invoice_id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tblinvoices.userid', 'like', "%$search%")
                ->orWhere('tblinvoices.invoicenum', 'like', "%$search%")
                ->orWhere('tblinvoices.date', 'like', "%$search%")
                ->orWhere('tblinvoices.total', 'like', "%$search%")
                ->orWhere('tblinvoices.status', 'like', "%$search%")
                ->orWhere('tblinvoices.paymentmethod', 'like', "%$search%")
                ->orWhere('tblclients.firstname', 'like', "%$search%")
                ->orWhere('tblclients.lastname', 'like', "%$search%");
            });
        }

        return $query->count();
    }


    /* Get Products */ 
    public function getProductsDataTable($start, $length, $search = '')
    {
        $query = Capsule::table('tblproducts')
            ->select('id', 'gid', 'name');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('gid', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            });
        }

        $products = $query->skip($start)->take($length)->get();


        $data = [];
        foreach ($products as $product) {
            $sync_product = Capsule::table('mod_synced_products')->where('pid', $product->id)->first();
            
            // Check if Product already sync 
            if (!$sync_product) {
                $data[] = [
                    'check_box' => '<input type="checkbox" name="selectedclients[]" value="' . $product->id . '" class="checkall">',
                    'product_name' => $product->name,
                    'group_name' => $this->getGroupName($product->gid),
                    'action_btns' => '
                    <a class="btn btn-primary btn-sm product-syn-btn" data-productid="' . $product->id . '">
                        <i class="fas fa-sync" id="sync-icon-' . $product->id . '"></i> Sync
                    </a>'
                ];
            }
        }

        return $data;
    }
    public function getProductsCount($search = '')
    {
        $query = Capsule::table('tblproducts')
            ->leftJoin('mod_synced_products', 'tblproducts.id', '=', 'mod_synced_products.pid')
            ->whereNull('mod_synced_products.pid');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tblproducts.gid', 'like', "%$search%")
                ->orWhere('tblproducts.name', 'like', "%$search%");
            });
        }

        return $query->count();
    }


    /* Get Logs */ 
    public function getLogsDataTable($start, $length, $search = '')
    {
        $query = Capsule::table('mod_bukkucrm_logs')
            ->select('id', 'action', 'request', 'http_code', 'response', 'datetime')
            ->orderBy('id', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                    ->orWhere('request', 'like', "%$search%")
                    ->orWhere('http_code', 'like', "%$search%")
                    ->orWhere('response', 'like', "%$search%")
                    ->orWhere('datetime', 'like', "%$search%");
            });
        }

        $logs = $query->skip($start)->take($length)->get();


        $data = [];
        foreach ($logs as $log) {
            
            $data[] = [
                'date' => $log->datetime,
                'action' => $log->action,
                'request' => $log->request,
                // 'status_code' => $log->http_code,
                'response' => "status_code: ". $log->http_code . "\n" .$log->response
            ];
        }

        return $data;
    }
    public function getLogsCount($search = '')
    {
        $query = Capsule::table('mod_bukkucrm_logs');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                    ->orWhere('request', 'like', "%$search%")
                    ->orWhere('http_code', 'like', "%$search%")
                    ->orWhere('response', 'like', "%$search%")
                    ->orWhere('datetime', 'like', "%$search%");
            });
        }

        return $query->count();
    }


    /* Create Contact, Client sync **/
    public function create_contact($id)
    {
        $token = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'access_hash')->first('value');
        $contact_type = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'contact_type')->value('value');
        if ($token) {
            $user = Capsule::table('tblclients')->where('id', $id)->first();

            $data = [
                "entity_type" => 'MALAYSIAN_INDIVIDUAL',
                "legal_name" => $user->firstname,
                "other_name" => $user->lastname,
                "reg_no_type" => "BRN",
                "reg_no" => $user->datecreated. "-". strtoupper(substr($user->firstname.$user->lastname, 0, 3)),
                "contact_persons" => [
                    [
                    "first_name" => "Random",
                    "last_name" => "User(Test)",
                    "is_default_billing" => false,
                    "is_default_shipping" => false
                    ]
                ],
                "types" => [$contact_type],
                "email" => $user->email,
                "phone_no" => preg_replace('/\D/', '', $user->phonenumber),
                "remarks" => "This is a remarks for the contact: ".$user->firstname.".",
                "receive_monthly_statement" => true,
                "receive_invoice_reminder" => true,
                "addresses" => [
                    [
                        "name" => $user->address1,
                        "street" => $user->address2,
                        "city" => $user->city,
                        "state" => $user->state,
                        "postcode" => $user->postcode,
                        "country_code" => $user->country,
                        "is_default_billing" => true,
                        "is_default_shipping" => true,
                    ]
                ]
            ];

            $api = new Api;

            $create_contact = $api->create_contact($data, $token);

            if ($create_contact['status_code'] == 200) {
                $create_contact['response'] = json_decode($create_contact['response'], true);
                $contact_id = $create_contact['response']['contact']['id'];

                $field_id = Capsule::table('tblcustomfields')->where('fieldname', 'like', 'bukkuClientID|%')->where('type', 'client')->value('id');
                $customFieldId = Capsule::table('tblcustomfieldsvalues')->where('fieldid', $field_id)->where('relid', $id)->value('id');

                // Store the custom field value for client
                if ($customFieldId) {
                    Capsule::table('tblcustomfieldsvalues')
                        ->where('id', $customFieldId)
                        ->update([
                            'value' => $contact_id
                        ]);
                } else {
                    Capsule::table('tblcustomfieldsvalues')->insert([
                        'fieldid' =>  $field_id,
                        'relid' => $user->id,
                        'value' => $contact_id
                    ]);
                }

                return ['status' => 'success', 'message' => 'Client created successfully.'];

            } else {
                $create_contact['response'] = json_decode($create_contact['response'], true);
                logActivity("Unable to syned the user: " .$id . ", Error:". $create_contact['response']['message']);
                return ['status' => 'warning', 'message' => $create_contact['response']['message']];
            }
        } else {
            return ['status' => 'error', 'message' => 'Access token is missing.'];
        }
    }

    /* Create Invoice, Invoice sync */
    public function create_invoice($id)
    {
        $token = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'access_hash')->first('value');
        if ($token) {

            $invoice = Capsule::table('tblinvoices')->where('id', $id)->first();
            $currency = getCurrency($invoice->userid);

            // Get contact details
            $field_id = Capsule::table('tblcustomfields')->where('fieldname', 'like', 'bukkuClientID|%')->where('type', 'client')->value('id');
            $contact_id = Capsule::table('tblcustomfieldsvalues')->where('fieldid', $field_id)->where('relid', $invoice->userid)->value('value');

            // Get service details
            $order = Capsule::table('tblorders')->where('invoiceid', $invoice->id)->first();
            $service = Capsule::table('tblhosting')->where('orderid', $order->id)->first();

            // get product id
            $hostingItem = Capsule::table('tblinvoiceitems')->where('invoiceid', $id)->where('type', 'Hosting')->first();
            $hosting = Capsule::table('tblhosting')->where('id', $hostingItem->relid)->first();
            $product = Capsule::table('tblproducts')->where('id', $hosting->packageid)->first();
            $product_id = Capsule::table('mod_synced_products')->where('pid', $hosting->packageid)->value('sync_pid');
            $form_description = !empty($product->short_description) ? $product->short_description : $product->name . ", description...";
            
            $client_data = Capsule::table('tblclients')->where('id', $invoice->userid)->first();
            $account_id = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'sale_acc_id')->value('value');

            $data = [
                "payment_mode" => "credit",
                "contact_id" => $contact_id,
                "date" => $invoice->date,
                "currency_code" => $currency['code'],
                "exchange_rate" => $currency['rate'],
                "billing_party" => $client_data->address1.",\n".$client_data->address2.",\n".$client_data->city,
                "tax_mode" => "exclusive",
                "form_items" => [
                    [
                        "account_id"=> $account_id,
                        // "account_id"=> 20,
                        "description"=> $form_description,
                        "service_date"=> $service->regdate,
                        "product_id"=> $product_id,
                        "unit_price"=> $invoice->total,
                        "quantity"=> 1,
                        "classification_code" => "022"
                    ]
                ],
                "term_items" => [
                    [
                        "date" => $invoice->duedate,
                        "payment_due" => "100%",
                        "description" => "Full Payment"
                    ]
                ],
                "status" => "ready",
                "myinvois_action" => "VALIDATE"
            ];

            $api = new Api;

            $create_invoice = $api->create_invoice($data, $token);

            if ($create_invoice['status_code'] == 200) {
                $create_invoice['response'] = json_decode($create_invoice['response'], true);

                $sync_invoice_id = $create_invoice['response']['transaction']['id'];
                $sync_product_id = $create_invoice['response']['transaction']['form_items'][0]['product_id'] ?? 0;
                $whmcs_productId = $hosting->packageid ?? 0;
                
                // Insert invoice data in custom table
                $this->insert_cstmInvoiceData(
                    $invoice->id,
                    $whmcs_productId,
                    $invoice->userid,
                    $create_invoice['response']['transaction']['contact_id'],
                    $sync_invoice_id,
                    $sync_product_id
                );

                return ['status' => 'success', 'message' => 'Invoice created successfully.'];
            } else {
                $create_invoice['response'] = json_decode($create_invoice['response'], true);
                return ['status' => 'warning', 'message' => $create_invoice['response']['message']];
            }
        } else {
            return ['status' => 'error', 'message' => 'Access token is missing.'];
        }
    }

    /* Create Product, Product sync */
    public function create_product($id)
    {
        $api = new Api;

        $token = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'access_hash')->first('value');
        if ($token) {
            $product = Capsule::table('tblproducts')->where('id', $id)->first();
            $sale_acc_id = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'sale_acc_id')->value('value');
            $purchase_acc_id = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'purchase_acc_id')->value('value');
            if(!$sale_acc_id) {
                $create_sale_acc = $api->create_sale_acc($token); 
                if($create_sale_acc['status_code'] == 200) {
                    $create_sale_acc['response'] = json_decode($create_sale_acc['response'], true);
                    $create_sale_acc_id = $create_sale_acc['response']['account']['id'];
                    $this->insert_accountData($create_sale_acc_id, 'sale_acc_id');
                }
            }
            if(!$purchase_acc_id) {
                $create_purchase_acc = $api->create_purchase_acc($token); 
                if($create_purchase_acc['status_code'] == 200) {
                    $create_purchase_acc['response'] = json_decode($create_purchase_acc['response'], true);
                    $create_purchase_acc_id = $create_purchase_acc['response']['account']['id'];
                    $this->insert_accountData($create_purchase_acc_id, 'purchase_acc_id');
                }
            }

            $syn_gid = Capsule::table('mod_synced_productgroups')->where('gid', $product->gid)->first();
            if(!$syn_gid) {
                $data = [
                    "name" => $this->getGroupName($product->gid),
                    "product_ids" => []
                ];
                $create_productGroup = $api->create_productGroup($data, $token);
                if($create_productGroup['status_code'] == 200) {
                    $create_productGroup['response'] = json_decode($create_productGroup['response'], true);
                    $res_gid = $create_productGroup['response']['group']['id'];
                    $res_name = $create_productGroup['response']['group']['name'];
                    $this->insert_syncedGroup($product->gid, $res_name, $res_gid);
                }
            }

            $product_price = Capsule::table('tblpricing')->where('type', 'product')->where('relid', $product->id)->value('monthly');

            $sale_id = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'sale_acc_id')->value('value');
            $purchase_id = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'purchase_acc_id')->value('value');

            // $random_sku = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5); 
            $data = [
                "name" => $product->name,
                "is_selling" => true,
                "sale_account_id" => $sale_id,
                // "sale_account_id" => 20,
                "is_buying" => true,
                "purchase_account_id" => $purchase_id,
                // "purchase_account_id" => 32,
                "track_inventory" => false,
                "units" => [
                    [
                        "label" => "unit",
                        "rate" => 1,
                        "sale_price" => $product_price,
                        "purchase_price" => $product_price,
                        "is_base" => true,
                        "is_sale_default" => true
                    ]
                ]
            ];

            $create_product = $api->create_product($data, $token); 

            if ($create_product['status_code'] == 200) {

                $create_product['response'] = json_decode($create_product['response'], true);
                
                // Insert product data in custom table
                $this->insert_cstmProductData(
                    $product->id,
                    $product->gid,
                    $create_product['response']['product']['id'],
                    $create_product['response']['product']['name'],
                    $sync_gid ?? ''
                );

                logActivity("Product Synced successsfully, product_id: " . $id);
                return ['status' => 'success', 'message' => 'Product synced/created successfully.'];

            } else {
                $create_product['response'] = json_decode($create_product['response'], true);
                logActivity("Unable to syned the user: " .$id . ", Error:". $create_product['response']['message']);
                return ['status' => 'error', 'message' => $create_product['response']['message']];
            }
        } else {
            return ['status' => 'error', 'message' => 'Access token is missing.'];
        }
    }


    /* Get Product Group name **/
    public function getGroupName($id) {
        return Capsule::table('tblproductgroups')->where('id', $id)->value('name');
    }

    /* Insert invoice data in Custom table */
    public function insert_cstmInvoiceData($invoice_id, $pid, $user_id, $contact_id, $sync_InvoiceId, $sync_pid) {
        try {
            return Capsule::table('mod_synced_invoices')->insert([
                'invoice_id' => $invoice_id,
                'product_id' => $pid,
                'user_id' => $user_id,
                'contact_id' => $contact_id,
                'sync_invoiceID' => $sync_InvoiceId,
                'sync_productID' => $sync_pid,
            ]);
        } catch (\Exception $e) {
            logModuleCall('bukkucrm', 'mod_synced_invoices', 'Error', $e->getMessage());
            return false;
        }
    }

    /* Insert Product data in Custom table */
    public function insert_cstmProductData($pid, $gid, $sync_pid, $name, $sync_gid) {
        try {
            return Capsule::table('mod_synced_products')->insert([
                'pid' => $pid,
                'gid' => $gid,
                'name' => $name,
                'sync_pid' => $sync_pid,
                'sync_gid' => $sync_gid,
            ]);
        } catch (\Exception $e) {
            logModuleCall('bukkucrm', 'mod_synced_products', 'Error', $e->getMessage());
            return false;
        }
    }

    /* Store activity in custom logs */
    public function bukkucrsLogs($action, $request, $httpCode, $response) {
        try {
            return Capsule::table('mod_bukkucrm_logs')->insert([
                'action' => $action,
                'request' => $request,
                'http_code' => $httpCode,
                'response' => $response,
            ]);
        } catch(Exception $e) {
            logModuleCall('bukkucrm', 'bukkucrsLogs', 'Error in mod_bukkucrm_logs log entry:', $e->getMessage());
        }
    }

    /* insert account details */
    public function insert_accountData($id, $action) {
        try {
            return Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', $action)->update([
                'value' => $id,
            ]);
        } catch(Exception $e) {
            logModuleCall('bukkucrm', 'bukkucrsLogs', 'Error in mod_bukkucrm_logs log entry:', $e->getMessage());
        }
    }

    /* Store synced productGroup in custom logs */
    public function insert_syncedGroup($gid, $name, $sync_gid) {
        try {
            return Capsule::table('mod_synced_productgroups')->insert([
                'name' => $name,
                'gid' => $gid,
                'sync_gid' => $sync_gid,
            ]);

        } catch(Exception $e) {
            logModuleCall('bukkucrm', 'bukkucrsLogs', 'Error in mod_synced_productgroups log entry:', $e->getMessage());
        }
    }
    
    /* Reset module logs */
    public function delete_logs($action) {
        if($action == 'delete_logs') {
            $deleted = Capsule::table('mod_bukkucrm_logs')->truncate(); 

            if ($deleted === null) { 
                return ['status' => 'success', 'message' => 'Module logs reset successfully.'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to reset the module logs.'];
            }
        }
    }
}
