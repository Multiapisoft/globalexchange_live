<?php
$title = "Users Login Details";
include_once 'header.php';
$query = "SELECT l.uid, l.datetime, l.ip, u.login_id, u.name FROM user_login_detail as l"
        . " LEFT JOIN user as u ON u.uid=l.uid"
        . " ORDER BY l.datetime DESC";
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
                                <th>User Id</th>
                                <?php /*<th>Name</th>*/?>
                                <th>Date</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = my_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->login_id;?></td>
                                <?php /*<td><?php echo $row->name;?></td>*/?>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->ip;?></td>
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