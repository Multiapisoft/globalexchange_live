<?php
$title = "Withdrawal Crypto history";
include_once 'header.php';
include_once '../lib/config.php'; // Include your database config
$type = (isset($_GET['type']) && (int) $_GET['type'] <= 2) ? (int) $_GET['type'] : 0;
$status_arr = array('Pending', 'Success', 'Failed');

// Function to get total amounts by status
function getTotalAmountByStatus($status) {
    $query = "SELECT SUM(net_amount) as total_amount FROM withdrawal_block WHERE status = '".$status."'";
    $result = my_query($query);
    $row = my_fetch_object($result);
    return $row->total_amount ? floatval($row->total_amount) : 0;
}

// Get totals for each status
$total_pending = getTotalAmountByStatus(0);  // Status 0 = Pending
$total_success = getTotalAmountByStatus(1);  // Status 1 = Success/Approved
$total_failed = getTotalAmountByStatus(2);   // Status 2 = Failed/Cancelled
?>

<!-- Summary Cards -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-4">
        <div class="panel panel-warning">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-clock-o fa-3x" style="color: #f0ad4e;"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 24px; font-weight: bold; color: #f0ad4e;">
                            <?php echo number_format($total_pending, 2); ?>
                        </div>
                        <div style="font-size: 14px; color: #666;">Total Pending Amount</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-check-circle fa-3x" style="color: #5cb85c;"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 24px; font-weight: bold; color: #5cb85c;">
                            <?php echo number_format($total_success, 2); ?>
                        </div>
                        <div style="font-size: 14px; color: #666;">Total Approved Amount</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="panel panel-danger">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-times-circle fa-3x" style="color: #d9534f;"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 24px; font-weight: bold; color: #d9534f;">
                            <?php echo number_format($total_failed, 2); ?>
                        </div>
                        <div style="font-size: 14px; color: #666;">Total Failed Amount</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <form action="withdrawal_block_model.php" method="post">
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
                <div class="form-group row">
                    <hr />
                    <button type="submit" class="btn btn-success" name="approve">Approve</button> <button type="submit" class="btn btn-danger" name="cancel">Cancel</button>
                </div>
                <div class="table-responsive">
                    <table id="dataTableDynamic" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><input type="checkbox" onclick="selectAll(this)" id="selectall"/></th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Fee</th>
                                <!--<th>Token</th>-->
                                <th>Net Amount</th>
                                <!--<th>Coin Value</th>-->
                                <th>Address</th>
                                <th>Remark</th>
                                <!--<th>Topup</th>-->
                                <th>T Wallet</th>
                                <th>B Wallet</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                </form>
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
			url:"report_withdrawal_block_list.php?type=<?php echo $type;?>",
			type:"POST"
		},
        "columnDefs": [
            { "orderable": false, "targets": [12] }
        ]
	});
});
function set_type(type){
    window.location = "report_withdrawal_block.php?type="+type;
}
function selectAll(source) {
  var checkboxes = document.querySelectorAll('.wid'); 
  for(i=0;i<checkboxes.length;i++)
     checkboxes[i].checked = source.checked;
}
$(function() {
  $(document).on('click', '#checkAll', function() {
    if ($(this).val() == 'Check All') {
      $('.wid').prop('checked', true);
      $(this).val('Uncheck All');
    } else {
      $('.wid').prop('checked', false);
      $(this).val('Check All');
    }
  });
});
</script>