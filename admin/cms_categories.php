<?php
$title = "CMS Categories";
include_once 'header.php';
$query = "SELECT c.*, cm.title as menu FROM cms_categories as c LEFT JOIN cms_menu as cm ON cm.recid = c.mid WHERE cm.status = 0 ORDER BY c.datetime DESC";
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
                                <th>Type</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = my_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->menu;?></td>
                                <td><?php echo $row->title;?></td>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->status;?></td>
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