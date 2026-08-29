<?php
$title = "Down line Details";
include_once 'header.php';
$uid = 100;
$i=0;
$j=0;
$child_levels = get_child_levels($uid, $with='yes');
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
                                <?php /*<th>Name</th>
                                <th>Mobile</th>*/?>
                                <th>DOJ</th>
                                <th>Sponsor</th>
                                <th>Placement</th>
                                <?php /*<th>Position</th>*/?>
                                <th>Package</th>
                                <th>Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($child_levels as $key => $child_level){
                            $uids = implode(" , ", $child_level);
                            if(!$uids){$uids = 0;}
                            $query = "SELECT u.uid, u.login_id, u.name, u.mobile, u.datetime, s.uid as sponsoruid, s.login_id as sponsor, p.uid as placementuid, p.login_id as placement, u.position, u.package, u.topup FROM user as u"
                            . " LEFT JOIN user as s ON s.uid=u.refer_id"
                            . " LEFT JOIN user as p ON p.uid=u.placement_id"
                            . " WHERE u.uid IN ($uids)";
                            $result = my_query($query);
                            while ($row = my_fetch_object($result)){$i++;?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->uid;?></td>
                                <?php /*<td><?php echo $row->name;?></td>
                                <td><?php echo $row->mobile;?></td>*/?>
                                <td><?php echo date("d M, Y h:i A", strtotime($row->datetime));?></td>
                                <td><?php echo $row->sponsoruid;?></td>
                                <td><?php echo $row->placementuid;?></td>
                                <?php /*<td><?php echo $row->position;?></td>*/?>
                                <td><?php echo $row->topup*1;?></td>
                                <td><?php echo $j;?></td>
                            </tr>
                            <?php }$j++;}?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>