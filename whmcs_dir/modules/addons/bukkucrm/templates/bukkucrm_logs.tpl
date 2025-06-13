{include file=$tplVar.header}

<h2>{$LANG['logs_list']}</h2>

<table id="logsTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th width="120">{$LANG['table_logs_date']}</th>
            <th width="120">{$LANG['table_logs_action']}</th>
            <th>{$LANG['table_logs_request']}</th>
            {* <th width="120">{$LANG['table_logs_status_code']}</th> *}
            <th>{$LANG['table_logs_response']}</th>
        </tr>
    </thead>
</table>
