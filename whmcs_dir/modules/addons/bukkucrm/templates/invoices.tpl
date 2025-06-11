{include file=$tplVar.header}
{include file=$tplVar.modals}


<h2>{$LANG['invoices_list']}</h2>

<table id="invoiceTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th width="20"><input type="checkbox" id="checkallInvoices"></th>
            <th>{$LANG['table_invoice_id']}</th>
            <th>{$LANG['table_invoiceClient']}</th>
            <th>{$LANG['table_invoice_date']}</th>
            <th>{$LANG['table_invoice_total']}</th>
            <th>{$LANG['table_invoice_status']}</th>
            <th>{$LANG['table_action_title']}</th>
        </tr>
    </thead>
</table>

<div class="selcted-itms">
    <a class="btn btn-primary"> {$LANG['sync_selected_btn']}</a>
</div>