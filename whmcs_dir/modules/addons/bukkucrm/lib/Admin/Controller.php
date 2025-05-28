<?php

namespace WHMCS\Module\Addon\Bukkucrm\Admin;

use WHMCS\Database\Capsule;
use Smarty;

class Controller
{
    private $params;
    private $tplVar = [];
    private $tplFileName;

    public function __construct($params)
    {
        global $CONFIG;
        $this->params = $params;

        $module = $params['module'];
        $this->tplVar['rootURL'] = $CONFIG['SystemURL'];
        $this->tplVar['urlPath'] = $CONFIG['SystemURL'] . "/modules/addons/{$module}/";
        $this->tplVar['tplDIR'] = ROOTDIR . "/modules/addons/{$module}/templates/";
        $this->tplVar['moduleLink'] = $params['modulelink'];
    }

    public function clients()
    {
        // Fetch WHMCS clients
        $clients = Capsule::table('tblclients')->get();

        // echo "<pre>"; print_r($clients); die;

        // $this->tplVar['clients'] = $clients;
        // $this->tplFileName = 'admin';
        $this->tplFileName = $this->tplVar['tab'] = __FUNCTION__;

        $this->output();
    }

    function clientsAjax()
    {
        header('Content-Type: application/json');
        $request = $_GET;

        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $search = $request['search']['value'];

        $helper = new \WHMCS\Module\Addon\Bukkucrm\Helper();

        $data = $helper->getClientsDataTable($start, $length, $search);
        $totalRecords = $helper->getClientsCount();
        $filteredRecords = $helper->getClientsCount($search);

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
        exit;
    }

    private function output()
    {
        $smarty = new Smarty();
        $smarty->assign('tplVar', $this->tplVar);
        $smarty->display($this->tplVar['tplDIR'] . $this->tplFileName . '.tpl');
    }
}
