<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="../modules/addons/bukkucrm/assets/css/admin.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../modules/addons/bukkucrm/assets/js/admin.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

<div class="add_hdr">

    <div class="add_nav">
        <ul>
            <li class=""><a href="addonmodules.php?module=bukkucrm" class="ad_home {if $tplVar['tab'] =='clients'}active {/if} "><i class="fa fa-user" aria-hidden="true"></i> {$LANG['tab_clients']}</a></li>
            <li class=""><a href="addonmodules.php?module=bukkucrm&action=invoices" class="ad_home {if $tplVar['tab'] =='invoices'}active {/if} "><i class="fas fa-file-invoice"></i> {$LANG['tab_invoices']}</a></li>
            <li class=""><a href="addonmodules.php?module=bukkucrm&action=products" class="ad_home {if $tplVar['tab'] =='products'}active {/if} "><i class="fas fa-file-invoice"></i> {$LANG['tab_products']}</a></li>
        </ul>    
    </div>
</div>