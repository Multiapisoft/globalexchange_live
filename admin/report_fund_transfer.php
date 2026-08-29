<?php
$title = "Fund Transfer Report";
include_once 'header.php';
$query = "SELECT t.uid, t.amount, t.datetime, u.login_id, u.name, f.login_id as from_login_id, f.name as from_name, t.type, t.remark, t.from_uid, t.tamt FROM fund_transfer as t"
        . " LEFT JOIN user as u ON u.uid=t.uid"
        . " LEFT JOIN user as f ON f.uid=t.from_uid"
        . " ORDER BY t.datetime DESC";
$result = my_query( $query);
$i=0;
$fund_type = get_fund_type();
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
                                <th>From</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <!--<th>Token</th>-->
                                <th>Type</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->login_id;?></td>
                                <td><?php if($row->from_uid==0){echo 'Company';}else{echo $row->from_login_id;}?></td>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->amount;?></td>
                                <!--<td><?php echo $row->tamt;?></td>-->
                                <td><?php echo $fund_type[$row->type];?></td>
                                <td><?php echo $row->remark;?></td>
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