<?php $type = (isset($_GET['type']) && (int) $_GET['type'] <= 1) ? (int) $_GET['type'] : 0;
$title = ($type == 1) ? "Recharge History" : "Recharge History";
include_once 'header.php';
$type_arr = array('Online', 'Offline');
$query = "SELECT r.* FROM recharge as r"
        . " WHERE r.amount!=0 AND r.uid='".$uid."'"
        . " ORDER BY r.datetime DESC";
$result = my_query($query);
$i=0;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Operator</th>
                                <th>Number</th>
                                <th>Status</th>
                                <th>Transition Id</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <?php /*<th>Income</th>*/?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->operator;?></td>
                                <td><?php echo $row->number;?></td>
                                <td><?php echo $row->status;?>
                                <td><?php echo $row->transaction_id;?></td>
                                <td><?php echo $type_arr[$row->type];?></td>
                                <td><?php echo $row->amount;?></td>
                                <?php /*<td><?php echo $row->user_income;?></td>*/?>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>