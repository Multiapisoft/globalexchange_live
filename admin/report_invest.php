<?php
$title = "Investments";
include_once 'header.php';
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="dataTableDynamic" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <!--<th>Compounding Amount</th>-->
                                <th></th>
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
			url:"report_invest_list.php",
			type:"POST"
		},
        "columnDefs": [
            { "orderable": false, "targets": [0] }
        ]
	});
});
 </script>