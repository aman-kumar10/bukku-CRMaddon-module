$(document).ready(function () {
    // Initialize Clients Table
    $('#clientTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "../modules/addons/bukkucrm/lib/Ajax/clients.php",
        "columns": [
            { "data": "check_box", "orderable": false, "searchable": false },
            { "data": "id" },
            { "data": "name", "orderable": false, "searchable": false },
            { "data": "email", "orderable": false, "searchable": false },
            { "data": "companyname", "orderable": false, "searchable": false },
            { "data": "status", "orderable": false, "searchable": false },
            { "data": "action_btns", "orderable": false, "searchable": false }
        ]
    });

    // Initialize Invoices Table
    $('#invoiceTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "../modules/addons/bukkucrm/lib/Ajax/invoices.php",
        "columns": [
            { "data": "check_box", "orderable": false, "searchable": false },
            { "data": "id" },
            { "data": "client_name", "orderable": false, "searchable": false },
            { "data": "invoice_date", "orderable": false, "searchable": false },
            { "data": "total_amount", "orderable": false, "searchable": false },
            { "data": "status", "orderable": false, "searchable": false },
            { "data": "action_btns", "orderable": false, "searchable": false }
        ]
    });
    
    // Initialize Products Table
    $('#productTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "../modules/addons/bukkucrm/lib/Ajax/products.php",
        "columns": [
            { "data": "check_box", "orderable": false, "searchable": false },
            { "data": "product_name" },
            { "data": "group_name" },
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

    $('#checkallProducts').on('change', function () {
        $('.checkall').prop('checked', $(this).prop('checked'));
    });

    $('.checkall').on('change', function () {
        if (!$(this).prop('checked')) {
            $('#checkallClients').prop('checked', false);
        } else if ($('.checkall:checked').length === $('.checkall').length) {
            $('#checkallClients').prop('checked', true);
        }
    });



    // Syncs clients functionality
    let syncUserId = null;

    $(document).on('click', '.user-syn-btn', function () {
        syncUserId = $(this).data('userid');  
        $('#syncModal').css('display', 'block');
    });

    $(document).on('click', '.cancel-btn', function () {
        $('#syncModal').hide();
    });

    $(document).on('click', '#syncModal', function (e) {
        if ($(e.target).is('#syncModal')) {
            $('#syncModal').hide();
        }
    });

    $(document).on('click', '.yes-btn', function () {
        if (!syncUserId) return;

        $(".fa.fa-spinner").addClass("icon-spin");
        $(".icon-wrapper .fas.fa-user").css('font-size', '30px');

        $.ajax({
            url: '',
            type: 'POST',
            data: {
                form_action: 'create_contact',
                user_id: syncUserId
            },
            dataType: 'json',
            success: function (response) {
                console.log('Message:', response);
                $('#syncModal').hide();
                if (response.status === 'success') {
                    iziToast.success({
                        title: 'Success',
                        message: response.message,
                        position: 'topRight'
                    });
                    
                    $('#clientTable').DataTable().ajax.reload(null, false);

                } else if (response.status === 'warning') {
                    iziToast.warning({
                        title: 'Error',
                        message: response.message,
                        position: 'topRight'
                    });
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: response.message,
                        position: 'topRight'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                iziToast.error({
                    title: 'Error',
                    message: 'AJAX request failed: ' + error,
                    position: 'topRight'
                });
            },
            complete: function () {
                $(".fa.fa-spinner").removeClass("icon-spin");
                syncUserId = null; 
            }
        });
    });




    // Syncs invoices functionality
    let syncInvoiceId = null;

    $(document).on('click', '.invoice-syn-btn', function () {
        syncInvoiceId = $(this).data('invoiceid');  
        $('#invoiceSyncModal').css('display', 'block');
    });

    $(document).on('click', '.cancel-btn', function () {
        $('#invoiceSyncModal').hide();
    });

    $(document).on('click', '#invoiceSyncModal', function (e) {
        if ($(e.target).is('#invoiceSyncModal')) {
            $('#invoiceSyncModal').hide();
        }
    });

    $(document).on('click', '.yes-btn', function () {
        if (!syncInvoiceId) return;

        $(".fa.fa-spinner").addClass("icon-spin");
        $(".icon-wrapper .fas.fa-file-invoice").css('font-size', '30px');

        $.ajax({
            url: '',
            type: 'POST',
            data: {
                form_action: 'create_invoice',
                invoice_id: syncInvoiceId
            },
            dataType: 'json',
            success: function (response) {
                console.log('Message:', response);
                $('#invoiceSyncModal').hide();
                if (response.status === 'success') {
                    iziToast.success({
                        title: 'Success',
                        message: response.message,
                        position: 'topRight'
                    });
                    
                    $('#invoiceTable').DataTable().ajax.reload(null, false);

                } else if (response.status === 'warning') {
                    iziToast.warning({
                        title: 'Error',
                        message: response.message,
                        position: 'topRight'
                    });
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: response.message,
                        position: 'topRight'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                iziToast.error({
                    title: 'Error',
                    message: 'AJAX request failed: ' + error,
                    position: 'topRight'
                });
            },
            complete: function () {
                $(".fa.fa-spinner").removeClass("icon-spin");
                syncInvoiceId = null; 
            }
        });
    });

    

    // Syncs products functionality
    let syncProductId = null;

    $(document).on('click', '.product-syn-btn', function () {
        syncProductId = $(this).data('productid');  
        $('#productSyncModal').css('display', 'block');
    });

    $(document).on('click', '.cancel-btn', function () {
        $('#productSyncModal').hide();
    });

    $(document).on('click', '#productSyncModal', function (e) {
        if ($(e.target).is('#productSyncModal')) {
            $('#productSyncModal').hide();
        }
    });

    $(document).on('click', '.yes-btn', function () {
        console.log(syncProductId);
        if (!syncProductId) return;

        $(".fa.fa-spinner").addClass("icon-spin");
        // $(".icon-wrapper .fas.fa-tag").css('font-size', '30px');
        $(".icon-wrapper .fas.fa-tag").css('display', 'none');

        $.ajax({
            url: '',
            type: 'POST',
            data: {
                form_action: 'create_product',
                product_id: syncProductId
            },
            dataType: 'json',
            success: function (response) {
                console.log('Message:', response);
                $('#productSyncModal').hide();
                if (response.status === 'success') {
                    iziToast.success({
                        title: 'Success',
                        message: response.message,
                        position: 'topRight'
                    });
                    
                    $('#productTable').DataTable().ajax.reload(null, false);

                } else if (response.status === 'warning') {
                    iziToast.warning({
                        title: 'Error',
                        message: response.message,
                        position: 'topRight'
                    });
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: response.message,
                        position: 'topRight'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                iziToast.error({
                    title: 'Error',
                    message: 'AJAX request failed: ' + error,
                    position: 'topRight'
                });
            },
            complete: function () {
                $(".fa.fa-spinner").removeClass("icon-spin");
                syncProductId = null; 
                $(".icon-wrapper .fas.fa-tag").css('display', 'block');
            }
        });
    });


});

