<?php
$title = "Buy History";
include_once 'header.php';
$status_arr = array('Panding', 'Success', 'Failed');
$query = "SELECT f.*, u.login_id, u.name FROM buy_tkn as f"
        . " LEFT JOIN user as u ON u.uid=f.uid"
        . " WHERE f.uid!=0"
        . " ORDER BY f.datetime DESC";
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
                                <th>Amount</th>
                                <th>Fee</th>
                                <th>Net Amount</th>
                                <th>Coin Value</th>
                                <th>TxId</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->login_id;?></td>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->amount;?></td>
                                <td><?php echo $row->fee;?></td>
                                <td><?php echo $row->net_amount;?></td>
                                <td><?php echo $row->amount_coin;?></td>
                                <td><?php echo $row->txid;?></td>
                                <td><?php echo $row->type;?></td>
                                <td><?php echo $status_arr[$row->status];?></td>
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