<?php

namespace WHMCS\Module\Addon\Bukkucrm;

use WHMCS\Database\Capsule;

class Helper
{
    // 
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

        return $query->skip($start)->take($length)->get();
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

}
