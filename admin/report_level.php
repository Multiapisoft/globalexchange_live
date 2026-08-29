<?php $type = (isset($_GET['type']) && (int) $_GET['type'] <= 6) ? (int) $_GET['type'] : 0;
$title = ($type == 2) ? "Level ROI Income" : (($type == 1) ? "Level Income" : "subscription package- Generation Distribution");
if($type == 3){
    $title = "Gold Auto Pool Income";
}
elseif($type == 4){
    $title = "Diamond Auto Pool Income";
}
elseif($type == 5){
    $title = "Royal Matrix Income";
}
elseif($type == 6){
    $title = "Crown Matrix Income";
}
include_once 'header.php';
$query = "SELECT l.*, u.login_id, u.name, f.login_id as from_login_id, f.name as from_name FROM income_level as l"
        . " LEFT JOIN user as u ON u.uid=l.uid"
        . " LEFT JOIN user as f ON f.uid=l.from_uid"
        . " WHERE l.type=".$type
        . " ORDER BY l.datetime DESC";
$result = my_query( $query);
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
                                <th>From</th>
                                <th>Date</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th><?php echo ($type == 3 || $type == 4) ? 'Level' : 'Level';?></th>
                                <?php if($type == 2 || $type == 3 || $type == 4){?>
                                <th>Rank</th>
                                <?php }?>
                                <?php if($type == 4){?>
                                <th>Amount (U)</th>
                                <th>Amount (W)</th>
                                <?php }?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->uid;?></td>
                                <td><?php echo $row->from_uid;?></td>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->iamount*1;?></td>
                                <td><?php echo $row->amount*1;?></td>
                                <td><?php echo ($type == 3 || $type == 4) ? $row->level : $row->level;?></td>
                                <?php if($type == 2 || $type == 3 || $type == 4){?>
                                <td><?php echo $row->pool;?></td>
                                <?php }?>
                                <?php if($type == 4){?>
                                <td><?php echo $row->uamt*1;?></td>
                                <td><?php echo $row->wamt*1;?></td>
                                <?php }?>
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