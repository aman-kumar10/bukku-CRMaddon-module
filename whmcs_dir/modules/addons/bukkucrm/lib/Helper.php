<?php

namespace WHMCS\Module\Addon\Bukkucrm;

use WHMCS\Database\Capsule;

require_once __DIR__ . '/../../../../init.php';


class Helper
{
    // get clients
    public function getClientsDataTable($start, $length, $search = '')
    {
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

            $data[] = [
                'check_box' => '<input type="checkbox" name="selectedclients[]" value="'.$client->id.'" class="checkall">',
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'companyname' => $client->companyname,
                'status' => $client->status,
                'created_at' => $client->created_at,
                'action_btns' => '
                    <a class="btn btn-primary btn-sm user-syn-btn" data-userid="' . $client->id . '"><i class="fas fa-sync" id="sync-icon-' . $client->id . '"></i> Sync</a>'
            ];
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
                'check_box' => '<input type="checkbox" name="selectedinvoices[]" value="'.$invoice->id.'" class="checkall">',
                'id' => $invoice->invoicenum,
                'client_name' => $clientname,
                'invoice_date' => $invoice->date,
                'due_date' => $invoice->duedate,
                'total_amount' => $currency['prefix'] . $invoice->total . $currency['suffix'],
                'payment_method' => $invoice->paymentmethod,
                'status' => $invoice->status,
                'action_btns' => '
                    <a class="btn btn-primary btn-sm user-syn-btn" data-userid="' . $invoice->id . '"><i class="fas fa-sync" id="sync-icon-' . $invoice->id . '"></i> Sync</a>'
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


}


