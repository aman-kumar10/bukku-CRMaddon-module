<?php

namespace WHMCS\Module\Addon\Bukkucrm\Admin;

use WHMCS\Module\Addon\Bukkucrm\Helper;
use WHMCS\Database\Capsule;

use Smarty;

class Controller
{
    private $params;

    private $tplVar = [];

    private $tplFileName;

    public $smarty;

    private $lang = [];

    /**
     * Constructor initializes parameters, paths, and language
     */
    public function __construct($params)
    {
        global $CONFIG;
        $this->params = $params;

        $module = $params['module'];

        $this->tplVar['rootURL']     = $CONFIG['SystemURL'];
        $this->tplVar['urlPath']     = $CONFIG['SystemURL'] . "/modules/addons/{$module}/";
        $this->tplVar['tplDIR']      = ROOTDIR . "/modules/addons/{$module}/templates/";
        $this->tplVar['header']      = ROOTDIR . "/modules/addons/{$module}/templates/header.tpl";
        $this->tplVar['modals']      = ROOTDIR . "/modules/addons/{$module}/templates/modals.tpl";
        $this->tplVar['moduleLink']  = $params['modulelink'];

        $adminLang = $_SESSION['adminlang'] ?? 'english';
        $langFile  = __DIR__ . "/../../lang/{$adminLang}.php";

        if (!file_exists($langFile)) {
            $langFile = __DIR__ . "/../../lang/english.php";
        }

        global $_ADDONLANG;
        include($langFile);
        $this->lang = $_ADDONLANG;
    }

    /**
     * Client tab handler
     */
    public function clients()
    {
        global $whmcs;
        $helper = new Helper;

        // Client synchronization
        if (isset($_REQUEST['form_action']) && $_REQUEST['form_action'] == 'create_contact') {
            $response = $helper->create_contact($_REQUEST['user_id']);

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $this->tplFileName = $this->tplVar['tab'] = __FUNCTION__;
        $this->output();
    }

    /**
     * Invoice tab handler
     */
    public function invoices()
    {
        global $whmcs;
        $helper = new Helper;

        // Invoice synchronization
        if (isset($_REQUEST['form_action']) && $_REQUEST['form_action'] == 'create_invoice') {
            $response = $helper->create_invoice($_REQUEST['invoice_id']);

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $this->tplFileName = $this->tplVar['tab'] = __FUNCTION__;
        $this->output();
    }

    /**
     * Product tab handler
     */
    public function products()
    {
        global $whmcs;
        $helper = new Helper;

        // Product synchronization
        if (isset($_REQUEST['form_action']) && $_REQUEST['form_action'] == 'create_product') {
            $response = $helper->create_product($_REQUEST['product_id']);

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $this->tplFileName = $this->tplVar['tab'] = __FUNCTION__;
        $this->output();
    }

    /**
     * Loads the assigned Smarty template
     */
    public function output()
    {
        $smarty = new Smarty();

        $smarty->assign('tplVar', $this->tplVar);
        $smarty->assign('LANG', $this->lang);

        $smarty->display($this->tplVar['tplDIR'] . $this->tplFileName . '.tpl');
    }
}
