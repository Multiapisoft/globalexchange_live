<?php $type = (isset($_GET['type']) && (int) $_GET['type'] <= 3) ? (int) $_GET['type'] : 0;
$title = ($type == 3) ? "Pool Income" : (($type == 2) ? "Royalty Income" : (($type == 1) ? "Fast Track Bonus" : "Reward Income"));
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
                                <th>Date</th>
                                <th>Amount</th>
                                <?php if($type == 0 || $type == 1 || 1){?>
                                <th>Rank</th>
                                <?php /*<th>Week</th>*/?>
                                <?php }?>
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
			url:"report_royalty_list.php?type=<?php echo $type;?>",
			type:"POST"
		},
        "columnDefs": [
            { "orderable": false, "targets": [0] }
        ]
	});
});
</script>