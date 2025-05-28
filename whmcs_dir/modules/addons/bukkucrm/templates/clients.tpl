<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="../modules/addons/bukkucrm/assets/css/admin.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<h2>Registered Client's List</h2>

<table id="clientTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Client ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
    </thead>
</table>

<script>
    $(document).ready(function () {
        $('#clientTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "../modules/addons/bukkucrm/lib/ajax/clients.php",
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": "companyname" },
                { "data": "status" },
                { "data": "created_at" }
            ]
        });
    });
</script>
