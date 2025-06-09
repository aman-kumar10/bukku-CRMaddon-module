{include file=$tplVar.header}
{include file=$tplVar.modals}


<h2>{$LANG['clients_list']}</h2>

<table id="clientTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th width="20"><input type="checkbox" id="checkallClients"></th>
            <th>{$LANG['table_client_id']}</th>
            <th>{$LANG['table_client_name']}</th>
            <th>{$LANG['table_client_email']}</th>
            <th>{$LANG['table_client_company']}</th>
            <th>{$LANG['table_client_status']}</th>
            <th>{$LANG['table_action_title']}</th>
        </tr>
    </thead>
</table>


<div class="selcted-itms">
    <a class="btn btn-primary"><img src="" alt=""> {$LANG['sync_selected_btn']}</a>
</div>