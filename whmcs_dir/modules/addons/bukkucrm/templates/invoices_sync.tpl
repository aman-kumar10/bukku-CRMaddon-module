{include file=$tplVar.header}
{include file=$tplVar.modals}


<h2>Invoices</h2>

<table id="invoiceTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th width="20"><input type="checkbox" id="checkallInvoices"></th>
            <th>Invoice ID</th>
            <th>Client Name</th>
            <th>Invoice Date</th>
            <th>Due Date</th>
            <th>Total</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<div class="selcted-itms text-center">
    <a class="btn btn-primary">  Sync Selected</a>
</div>