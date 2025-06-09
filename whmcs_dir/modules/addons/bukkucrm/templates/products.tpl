{include file=$tplVar.header}
{include file=$tplVar.modals}


<h2>{$LANG['products_list']}</h2>

<table id="productTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th width="20"><input type="checkbox" id="checkallProducts"></th>
            <th>{$LANG['table_product_name']}</th>
            <th>{$LANG['table_group_name']}</th>
            <th>{$LANG['table_action_title']}</th>
        </tr>
    </thead>
</table>


<div class="selcted-itms">
    <a class="btn btn-primary"><img src="" alt=""> {$LANG['sync_selected_btn']}</a>
</div>