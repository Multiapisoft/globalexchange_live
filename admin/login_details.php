<?php
$title = "Login Details";
include_once 'header.php';
$query = "SELECT l.datetime, l.ip FROM admin_login_detail as l"
        . " ORDER BY l.datetime DESC";
$result = my_query($query);
$i = 0;
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
                                <th>Date</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
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