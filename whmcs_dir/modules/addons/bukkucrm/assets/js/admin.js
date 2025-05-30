// Get Clients
$(document).ready(function () {
    $('#clientTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "../modules/addons/bukkucrm/lib/ajax/clients.php",
        "columns": [
            {"data": "check_box", "orderable": false, "searchable": false},
            {"data": "id"},
            {"data": "name", "orderable": false, "searchable": false},
            {"data": "email", "orderable": false, "searchable": false},
            {"data": "companyname", "orderable": false, "searchable": false},
            {"data": "status", "orderable": false, "searchable": false},
            {"data": "created_at", "orderable": false, "searchable": false},
            {"data": "action_btns", "orderable": false, "searchable": false}
        ]
    });
});

// Get Invoices
$(document).ready(function () {
    $('#invoiceTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "../modules/addons/bukkucrm/lib/ajax/invoices.php",
        "columns": [
            {"data": "check_box", "orderable": false, "searchable": false},
            {"data": "id"},
            {"data": "client_name", "orderable": false, "searchable": false},
            {"data": "invoice_date", "orderable": false, "searchable": false},
            {"data": "due_date", "orderable": false, "searchable": false},
            {"data": "total_amount", "orderable": false, "searchable": false},
            {"data": "payment_method", "orderable": false, "searchable": false},
            {"data": "status", "orderable": false, "searchable": false},
            {"data": "action_btns", "orderable": false, "searchable": false}
        ]
    });
});


// spin sync button
$(document).on('click', '.user-syn-btn', function (e) {

    var userId = $(this).data('userid');
    var icon = $('#sync-icon-' + userId);

    icon.addClass('spin');

    setTimeout(function () {
        icon.removeClass('spin');
    }, 3000);

});

// Check all
$(document).ready(function () {
    $('#checkallClients').on('change', function () {
        $('.checkall').prop('checked', $(this).prop('checked'));
    });

    $('#checkallInvoices').on('change', function () {
        $('.checkall').prop('checked', $(this).prop('checked'));
    });

    $('.checkall').on('change', function () {
        if (!$(this).prop('checked')) {
            $('#checkallClients').prop('checked', false);
        } else if ($('.checkall:checked').length === $('.checkall').length) {
            $('#checkallClients').prop('checked', true);
        }
    });


});