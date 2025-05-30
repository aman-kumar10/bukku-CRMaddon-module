<?php

namespace WHMCS\Module\Addon\Bukkucrm\Admin;

use WHMCS\Database\Capsule;
use Smarty;

class Controller
{
    private $params;
    private $tplVar = [];
    private $tplFileName;

    public $smarty;

    public function __construct($params)
    {
        global $CONFIG;
        $this->params = $params;

        $module = $params['module'];
        $this->tplVar['rootURL'] = $CONFIG['SystemURL'];
        $this->tplVar['urlPath'] = $CONFIG['SystemURL'] . "/modules/addons/{$module}/";
        $this->tplVar['tplDIR'] = ROOTDIR . "/modules/addons/{$module}/templates/";
        $this->tplVar['header'] = ROOTDIR . "/modules/addons/{$module}/templates/header.tpl";
        $this->tplVar['modals'] = ROOTDIR . "/modules/addons/{$module}/templates/modals.tpl";
        $this->tplVar['moduleLink'] = $params['modulelink'];
    }

    public function clients()
    {

        $this->tplFileName = $this->tplVar['tab'] = __FUNCTION__;
        $this->output();
    }
    public function invoices_sync()
    {
       
        $this->tplFileName = $this->tplVar['tab'] = __FUNCTION__;
        $this->output();
    }


    public function output()
    {
        $smarty = new Smarty();
        $smarty->assign('tplVar', $this->tplVar);
        $smarty->display($this->tplVar['tplDIR'] . $this->tplFileName . '.tpl');
    }

    // public function output()
    // {
    //     $this->smarty = new Smarty();
    //     $this->smarty->assign('tplVar', $this->tplVar);
    //     $this->smarty->assign('fileName', $this->tplFileName);
    //     // $this->smarty->assign('lang', $this->params["_lang"]);
    //     $this->smarty->compile_dir = $GLOBALS['templates_compiledir'];
    //     $this->smarty->caching = false;
    //     if (!empty($this->tplFileName)) {
    //         $this->smarty->display($this->tplVar['tplDIR'] . $this->tplFileName . '.tpl');
    //     } else {
    //         $this->tplVar['errorMsg'] = 'not found';
    //         $this->smarty->display($this->tplFileName . 'error.tpl');
    //     }
    // }
}
