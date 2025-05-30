<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="../modules/addons/bukkucrm/assets/css/admin.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../modules/addons/bukkucrm/assets/js/admin.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

{* {$tplVar|@print_r} *}
<div class="add_hdr">

    {* <a href="https://whmcsglobalservices.com/" class="aspire_logo" target="_blank"><img src="/modules/addons/zohobook/assets/img/whmcsglobalservices.svg"></a> *}
    <div class="add_nav">
        <ul>
            <li class=""><a href="addonmodules.php?module=bukkucrm" class="ad_home {if $tplVar['tab'] =='clients'}active {/if} "><i class="fa fa-user" aria-hidden="true"></i> Clients Sync</a></li>
            <li class=""><a href="addonmodules.php?module=bukkucrm&action=invoices_sync" class="ad_home {if $tplVar['tab'] =='invoices_sync'}active {/if} "><i class="fas fa-file-invoice"></i> Invoices Sync</a></li>
        </ul>    
    </div>
</div>