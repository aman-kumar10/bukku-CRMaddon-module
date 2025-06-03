<?php

namespace WHMCS\Module\Addon\Bukkucrm;

use WHMCS\Module\Addon\Bukkucrm\Api;

use WHMCS\Database\Capsule;

require_once __DIR__ . '/../../../../init.php';


class Helper
{
    // get clients
    function getClientsDataTable($start, $length, $search = '')
    {
        $field = Capsule::table('tblcustomfields')
            ->where('type', 'client')
            ->where('fieldname', 'like', 'bukkuClientID|%')
            ->first();

        if (!$field) {
            return [];
        }

        $field_id = $field->id;

        $query = Capsule::table('tblclients')
            ->select('id', Capsule::raw("CONCAT(firstname, ' ', lastname) AS name"), 'email', 'companyname', 'status', 'created_at');

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
            $customFieldValue = Capsule::table('tblcustomfieldsvalues')
                ->where('fieldid', $field_id)
                ->where('relid', $client->id)
                ->value('value');

            if (empty($customFieldValue)) {
                $data[] = [
                    'check_box' => '<input type="checkbox" name="selectedclients[]" value="' . $client->id . '" class="checkall">',
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'companyname' => $client->companyname,
                    'status' => $client->status,
                    'created_at' => $client->created_at,
                    'action_btns' => '
                    <a class="btn btn-primary btn-sm user-syn-btn" data-userid="' . $client->id . '" data-username="' . $client->name . '">
                        <i class="fas fa-sync" id="sync-icon-' . $client->id . '"></i> Sync
                    </a>'
                ];
            }
        }

        return $data;
    }

    // clients search count
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

    // Get invoices 
    public function getInvoiceDataTable($start, $length, $search = '')
    {
        $query = Capsule::table('tblinvoices')
            ->join('tblclients', 'tblclients.id', '=', 'tblinvoices.userid')
            ->select(
                'tblinvoices.id',
                'tblinvoices.userid',
                'tblinvoices.invoicenum',
                'tblinvoices.date',
                'tblinvoices.duedate',
                'tblinvoices.total',
                'tblinvoices.status',
                'tblinvoices.paymentmethod',
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
                    ->orWhere('tblinvoices.paymentmethod', 'like', "%$search%")
                    ->orWhere('tblclients.firstname', 'like', "%$search%")
                    ->orWhere('tblclients.lastname', 'like', "%$search%");
            });
        }

        $invoices = $query->skip($start)->take($length)->get();


        $data = [];
        foreach ($invoices as $invoice) {
            $clientname = $invoice->firstname . " " . $invoice->lastname;
            $currency = getCurrency($invoice->userid);

            $data[] = [
                'check_box' => '<input type="checkbox" name="selectedinvoices[]" value="' . $invoice->id . '" class="checkall">',
                'id' => $invoice->invoicenum,
                'client_name' => $clientname,
                'invoice_date' => $invoice->date,
                'due_date' => $invoice->duedate,
                'total_amount' => $currency['prefix'] . $invoice->total . $currency['suffix'],
                'payment_method' => $invoice->paymentmethod,
                'status' => $invoice->status,
                'action_btns' => '
                    <a class="btn btn-primary btn-sm invoice-syn-btn" data-userid="' . $invoice->id . '"><i class="fas fa-sync" id="sync-icon-' . $invoice->id . '"></i> Sync</a>'
            ];
        }

        return $data;
    }

    // Invoices search
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


    // Get User details
    public function create_contact($id)
    {
        $token = Capsule::table('tbladdonmodules')->where('module', 'bukkucrm')->where('setting', 'access_hash')->first('value');
        if ($token) {
            $user = Capsule::table('tblclients')->where('id', $id)->first();

            $data = [
                "entity_type" => 'MALAYSIAN_INDIVIDUAL',
                "legal_name" => $user->firstname,
                "other_name" => $user->lastname,
                "reg_no_type" => 'BRN',
                "reg_no" => "20230123012345",
                "old_reg_no" => null,
                "tax_id_no" => null,
                "sst_reg_no" => null,
                "contact_persons" => [
                    [
                        "first_name" => "Aisyah",
                        "last_name" => "binti Ismail",
                        "is_default_billing" => true,
                        "is_default_shipping" => false,
                    ],
                ],
                "email" => $user->email,
                // "phone_no" => preg_replace('/[^\d+]/', '', $user->phonenumber),
                "types" => ['customer'],
            ];

            $api = new Api;

            $create_contact = $api->create_contact($data, $token);

            if ($create_contact['status_code'] == 200) {
                $create_contact['response'] = json_decode($create_contact['response'], true);
                $contact_id = $create_contact['response']['contact']['id'];

                $field_id = Capsule::table('tblcustomfields')->where('fieldname', 'like', 'bukkuClientID|%')->where('type', 'client')->value('id');
                $customFieldIdForClient = Capsule::table('tblcustomfieldsvalues')->where('fieldid', $field_id)->where('relid', $user->id)->value('id');
                if ($customFieldIdForClient) {
                    Capsule::table('tblcustomfieldsvalues')
                        ->where('id', $customFieldIdForClient)
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
                return ['status' => 'warning', 'message' => $create_contact['response']['message']];
            }
        } else {
            return ['status' => 'error', 'message' => 'Access token is missing.'];
        }
    }
}
