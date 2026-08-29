<?php
$title = "CMS";
$mid = isset($_GET['mid']) ? (int) $_GET['mid'] : 0;
include '../lib/config.php';
$mrow = my_fetch_object(my_query("SELECT * FROM cms_menu WHERE recid='".$mid."' AND status = 0"));
$title = isset($mrow->title) ? $mrow->title : 'CMS';
include_once 'header.php';
if (!$mrow) {
    redirect('./cms_categories.php');
    die();
}
$query = "SELECT * FROM cms WHERE mid = '".$mid."' ORDER BY datetime DESC";
$result = my_query($query);
$i=0;
?>
<div class="row">
    <div class="col-sm-12">
        <a href="cms_add.php?mid=<?php echo $mid;?>" class="btn btn-success w-md m-b-10 pull-right" data-toggle="tooltip" data-placement="right" title="Add <?php echo $mrow->title;?>">Add <?php echo $mrow->title;?></a>
    </div>
</div>
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
                                <th>Description</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = my_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->title;?></td>
                                <td><?php echo $row->description;?></td>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td>
                                    <a href="cms_model.php?mid=<?php echo $mid;?>&&delete=<?php echo $row->recid;?>" onclick="return confirm('Are you sure for delete.');" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                </td>
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