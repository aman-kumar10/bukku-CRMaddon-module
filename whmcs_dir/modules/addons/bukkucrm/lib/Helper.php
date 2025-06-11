<?php

namespace WHMCS\Module\Addon\Bukkucrm;

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
        $query = Capsule::table('tblclients');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('companyname', 'like', "%$search%");
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
            ->join('tblclients', 'tblclients.id', '=', 'tblinvoices.userid');

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
                $q->where('type', 'like', "%$search%")
                    ->orWhere('gid', 'like', "%$search%")
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
        $query = Capsule::table('tblproducts');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%$search%")
                    ->orWhere('gid', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('paytype', 'like', "%$search%");
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
                "legal_name" => $user->firstname." ".$user->lastname,
                "other_name" => $user->companyname,
                "email" => $user->email,
                "types" => [$contact_type],
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

            $form_description = !empty($product->short_description) ? $product->short_description : $product->name . ", description...";

            $data = [
                "payment_mode" => "credit",
                "contact_id" => $contact_id,
                "date" => $invoice->date,
                "currency_code" => $currency['code'],
                "exchange_rate" => $currency['rate'],
                "tax_mode" => "inclusive",
                "form_items" => [
                    [
                        "account_id"=> 20,
                        "description"=> $form_description,
                        "service_date"=> $service->regdate,
                        "product_id"=> $hosting->packageid,
                        "unit_price"=> $invoice->total,
                        "quantity"=> 1
                    ]
                ],
                "term_items" => [
                    [
                        "date" => $invoice->duedate,
                        "payment_due" => "100%",
                        "description" => "Full Payment"
                    ]
                ],
                "status" => "draft",
                "myinvois_action" => "NORMAL"
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
        $token = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'access_hash')->first('value');
        if ($token) {
            $product = Capsule::table('tblproducts')->where('id', $id)->first();
            // $random_sku = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5); 
            $data = [
                "name" => $product->name,
                "is_selling" => true,
                "sale_account_id" => 20,
                "sale_tax_code_id" => 3,
                "is_buying" => false,
                "track_inventory" => false,
                "units" => [
                    [
                        "label" => "unit",
                        "rate" => 1,
                        "is_base" => true
                    ]
                ]
            ];


            $api = new Api;

            $create_contact = $api->create_product($data, $token); 

            if ($create_contact['status_code'] == 200) {

                $create_contact['response'] = json_decode($create_contact['response'], true);
                
                // Insert product data in custom table
                $this->insert_cstmProductData(
                    $product->id,
                    $product->gid,
                    $create_contact['response']['product']['id'],
                    $create_contact['response']['product']['name']
                );

                logActivity("Product Synced successsfully, product_id: " . $id);
                return ['status' => 'success', 'message' => 'Product synced/created successfully.'];

            } else {
                $create_contact['response'] = json_decode($create_contact['response'], true);
                logActivity("Unable to syned the user: " .$id . ", Error:". $create_contact['response']['message']);
                return ['status' => 'error', 'message' => $create_contact['response']['message']];
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
            logModuleCall('bukkucrm', 'insert_cstmInvoiceData', 'Error', $e->getMessage());
            return false;
        }
    }

    /* Insert Product data in Custom table */
    public function insert_cstmProductData($pid, $gid, $sync_pid, $name) {
        try {
            return Capsule::table('mod_synced_products')->insert([
                'pid' => $pid,
                'gid' => $gid,
                'name' => $name,
                'sync_pid' => $sync_pid,
                'sync_gid' => '',
            ]);
        } catch (\Exception $e) {
            logModuleCall('bukkucrm', 'insert_cstmInvoiceData', 'Error', $e->getMessage());
            return false;
        }
    }

    
}
