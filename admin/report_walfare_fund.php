<?php
$title = 'Walfare Fund Report';
include_once 'header.php';
$query = 'SELECT wb.uid, wb.fee, wb.datetime, u.login_id, u.name  FROM withdrawal_block as wb'
    . ' LEFT JOIN user as u ON u.uid=wb.uid'
    . ' WHERE wb.status = 1'
    . ' ORDER BY wb.datetime DESC';
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
                                <th>User</th>
                                <th>Amount</th>
                                <th>Date</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)) {
                                $i++; ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $row->login_id; ?></td>
                                <td><?php echo $row->fee; ?></td>
                                <td><?php echo date('d M, Y h:i A', strtotime($row->datetime)); ?></td>
                            
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>