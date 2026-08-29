<?php
$title = "Recharge History";
include_once 'header.php';
$type_arr = array('Online', 'Offline');
$query = "SELECT r.*, u.login_id, u.name as u_name FROM recharge as r"
        . " LEFT JOIN user as u ON u.uid=r.uid"
        . " WHERE r.amount!=0"
        . " ORDER BY r.datetime DESC";
$result = my_query($query);
$i=0;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="dataTableExample2" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Operator</th>
                                <th>Number</th>
                                <th>Status</th>
                                <th>Transition Id</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->login_id." (".$row->u_name.")";?></td>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->operator;?></td>
                                <td><?php echo $row->number;?></td>
                                <td><?php echo $row->status;?><?php /*if($row->status=='PENDING'){?><a href="recharge_status_change.php?recid=<?php echo $row->recid;?>"><?php echo $row->status;?></a><?php }else{echo $row->status;}*/?></td>
                                <td><?php echo $row->transaction_id;?></td>
                                <td><?php echo $type_arr[$row->type];?></td>
                                <td><?php echo $row->amount;?></td>
                                <td><?php echo $row->user_income;?></td>
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