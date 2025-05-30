$(document).ready(function () {
    // Get Clients
    $('#clientTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "../modules/addons/bukkucrm/lib/ajax/clients.php",
        "columns": [
            { "data": "check_box", "orderable": false, "searchable": false },
            { "data": "id" },
            { "data": "name", "orderable": false, "searchable": false },
            { "data": "email", "orderable": false, "searchable": false },
            { "data": "companyname", "orderable": false, "searchable": false },
            { "data": "status", "orderable": false, "searchable": false },
            { "data": "created_at", "orderable": false, "searchable": false },
            { "data": "action_btns", "orderable": false, "searchable": false }
        ]
    });

    // Get Invoices
    $('#invoiceTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "../modules/addons/bukkucrm/lib/ajax/invoices.php",
        "columns": [
            { "data": "check_box", "orderable": false, "searchable": false },
            { "data": "id" },
            { "data": "client_name", "orderable": false, "searchable": false },
            { "data": "invoice_date", "orderable": false, "searchable": false },
            { "data": "due_date", "orderable": false, "searchable": false },
            { "data": "total_amount", "orderable": false, "searchable": false },
            { "data": "payment_method", "orderable": false, "searchable": false },
            { "data": "status", "orderable": false, "searchable": false },
            { "data": "action_btns", "orderable": false, "searchable": false }
        ]
    });


    // Check all
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


    // User Sync Modal
    $(document).on('click', '.user-syn-btn', function () {
        const userId = $(this).data('userid');
        $('#sync-user-id').val(userId);
        const userName = $(this).data('username');
        $('#sync-user-name').val(userName);
        $('#syncModal').css('display', 'block');
    });

    $(document).on('click', '.cancel-btn', function () {
        $('#syncModal').hide();
    });

    $(document).on('click', '.ok-btn', function () {
        $('#completeModal').hide();
    });

    $(document).on('click', '.yes-btn', function () {
        $(".fa.fa-spinner").addClass("icon-spin");
        $(".icon-wrapper .fas.fa-user").css('font-size', '30px');
        setTimeout(function () {
            $(".fa.fa-spinner").removeClass("icon-spin");
            $('#syncModal').hide();
            $('#sync-userName').html($('#sync-user-name').val());
            $('#completeModal').show();
            $(".icon-wrapper .fas.fa-user").css('font-size', '40px');
        }, 3000);
    });


    // Invoice Sync Modal
    $(document).on('click', '.invoice-syn-btn', function () {
        $('#invoiceSyncModal').css('display', 'block');
    });

    $(document).on('click', '.cancel-btn', function () {
        $('#invoiceSyncModal').hide();
    });

    $(document).on('click', '.ok-btn', function () {
        $('#completeModal').hide();
    });

    $(document).on('click', '.yes-btn', function () {
        $(".fa.fa-spinner").addClass("icon-spin");
        $(".icon-wrapper .fas.fa-file-invoice").css('font-size', '30px');
        setTimeout(function () {
            $(".fa.fa-spinner").removeClass("icon-spin");
            $('#invoiceSyncModal').hide();
            $('#sync-userName').html('Invoice');
            $('#completeModal').show();
            $(".icon-wrapper .fas.fa-file-invoice").css('font-size', '40px');
        }, 3000);
    });
});

