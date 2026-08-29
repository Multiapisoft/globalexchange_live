<?php
$title = "Packages";
include_once 'header.php';
$check = my_query("SHOW COLUMNS FROM `investments_plan` LIKE 'action'");

if (mysqli_num_rows($check) == 0) {
    $query = "ALTER TABLE `investments_plan` ADD `action` TINYINT NOT NULL DEFAULT '1' COMMENT '1. active\r\n,0. Deactive' AFTER `status`;";
    my_query($query);
}
$query = "SELECT * FROM investments_plan WHERE status IN (0)";
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
                                <th>Title</th>
                                <th>Amount</th>
                                <th>%</th>
                                <th>% To</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = my_fetch_object($result)) {
                                $i++; ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $row->title; ?></td>
                                    <td><?php echo $row->amount_from * 1; ?></td>
                                    <td><?php echo $row->percentage * 1; ?></td>
                                    <td><?php echo $row->percentage_to * 1; ?></td>
                                    <td><?php
                                        if ($row->action == 1) {
                                            echo 'Active';
                                        } elseif ($row->action == 0) {
                                            echo 'Inactive';
                                        } elseif ($row->action == 2) {
                                            echo 'Coming Soon';
                                        }
                                        ?></td>
                                    <td>
                                        <a class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update" href="cms_package_edit.php?recid=<?php echo $row->recid; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    </td>
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