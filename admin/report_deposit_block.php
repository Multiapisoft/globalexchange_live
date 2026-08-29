<?php
$title = "Deposit Crypto History";
include_once 'header.php';
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 2) ? (int) $_GET['type'] : 0;
$status_arr = array('Panding', 'Success', 'Failed');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <div class="form-group row">
                    <label for="login_id" class="col-sm-3 col-form-label">Status</label>
                    <div class="col-sm-9">
                        <select name="type" id="type" class="form-control" onchange="set_type(this.value);">
                            <?php foreach ($status_arr as $key => $value){?>
                            <option value="<?php echo $key;?>" <?php if(isset($_GET['type']) && $_GET['type']==$key){echo "selected='selected'";}?>><?php echo $value;?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="dataTableDynamic" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Amount</th>

                                <th>Type</th>
                                <th>Status</th>
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
			url:"report_deposit_block_list.php?type=<?php echo $type;?>",
			type:"POST"
		},
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] }
        ]
	});
});
function set_type(type){
    window.location = "report_deposit_block.php?type="+type;
}
</script>