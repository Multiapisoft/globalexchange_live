<?php
$title = "Users";
include_once 'header.php';
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <a href="users_d.php">Download</a>
                <div class="table-responsive">
                    <table id="dataTableDynamic" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID No.</th>
                                <th>User Id</th>
                                <th>Sponsor</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>DOJ</th>
                                <th>Wallet</th>
                                <th>T Wallet</th>
                                <th>Package</th>
                                <th>Team B</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
	var dataTable = $('#dataTableDynamic').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"user_list.php",
			type:"POST"
		},
        "columnDefs": [
            { "orderable": false, "targets": [0,10] }
        ]
	});
});
 </script>