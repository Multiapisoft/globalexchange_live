<?php
$title = "Tree View";
include_once 'header.php';
$childs = get_single_dimensional(get_child_levels($uid, 'yes'));
if (isset($_GET['login_id'])) {
    $login_id = tres($_GET['login_id']);
    $no = registeredUserId($login_id);
    if ($no != 0) {
        $uid = $no;
    }
}
if (isset($_GET['no'])) {
    $no = tres($_GET['no']);
    if ($no != 0) {
        $uid = $no;
    }
}

if (!in_array($uid, $childs)) {
    redirect('tree_view.php');
}

$userF = get_user_details($uid);
$rsS = my_query("SELECT * FROM user WHERE placement_id = '$uid'");
?>
<style type="text/css">
    .child{
    }
    .child span {
        text-align: center;
        margin-top: -26px;
        width: 520px;
    }
    .user {
        margin-bottom: 10px;
        margin-top: 10px;
    }
    table {
        background-color: #121212;
        border-bottom: 1px solid rgba(212, 175, 55, 0.22);
        border-collapse: collapse;
        color: #f5f5f5;
        margin-bottom: 20px;
        width: 100%;
    }
    table td {
        border-right: 1px solid rgba(212, 175, 55, 0.22);
        padding: 8px 10px;
    }
    table td:hover {
        background-color: rgba(240, 185, 11, 0.08);
    }
    table th h4 {
        margin: 0;
    }
    table th {
        border-right: 1px solid rgba(212, 175, 55, 0.22);
        font-weight: normal;
        padding: 10px;
        text-align: left;
    }
    table td.center {
        text-align: center;
    }
    table td.last {
        background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
    }
    table th.center {
        text-align: center;
    }
    table th.last {
        background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
    }
    table tr {
        border-left: 1px solid rgba(212, 175, 55, 0.22);
        border-top: 1px solid rgba(212, 175, 55, 0.22);
    }
    table tr.caption {
        border-left: 1px solid rgba(212, 175, 55, 0.22);
    }

    .description-hover {
        background: none repeat scroll 0 0 #fff;
        border: 1px solid #ccc;
        height: 100px;
        left: -45px;
        opacity: 0;
        padding: 10px;
        position: absolute;
        top: 5px;
        transition: all 0.5s ease-in-out 0s;
        width: 300px;
        z-index: -1;
    }
    .tree-img{
        position: relative;
    }
    .tree-img:hover .description-hover {
        left: -150px;
        top: 100px;
        opacity: 1;
        z-index: 3;
    }
    .nameBox {
        border-bottom: 1px dashed #ccc;
        margin-bottom: 5px;
        padding-bottom: 5px;
    }
    .nameBox a {
        color: #173c57;
        font-size: 12px;
    }
    .nameBox a:hover {
        text-decoration: underline;
    }
    .nameBox p {
        color: #797979;
        font-size: 12px;
        margin: 0;
    }
    .description-hover h3 {
        color: #414141;
        font-size: 17px;
        margin: 3px 0 0;
    }
    .description-hover > p {
    }
</style>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <form class="form-horizontal" id="form-validate" action="tree_view.php" method="get">
            <div class="form-group row">
                <label for="login_id" class="col-xs-3 col-form-label">User Id *</label>
                <div class="col-xs-6">
                    <input class="form-control" type="text" id="login_id" name="no" maxlength="50" required="required" style="background: #fff;" />
                </div>
                <div class="col-xs-3">
                    <button type="submit" class="btn btn-success" id="submit">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div align="center">
            <div class="user">
                <a class="tree-img" href="tree_view.php?no=<?php echo $uid; ?>" title=""><br />
                    <?php echo $userF->uid; //$userF->login_id; ?><br />
                    <img src="images/user_<?php echo get_img_clr($userF); ?>.png" />
                    <!--/**********************/-->
                    <?php getTreeDescription($userF->uid, $userF->datetime, $userF->refer_id); ?>
                    <!--/**********************/-->
                </a>
            </div>
            <img src="images/line.png" style="width: 67%;" />
            <?php getTree($uid, 2);?>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>

<?php

function get_img_clr($user) {
    $black = 3;
    $green = 2;
    $red = 4;
    if ($user->status == 1) {
        $img = $black;
    } elseif ($user->topup > 0) {
        $img = $green;
    } else {
        $img = $red;
    }
    return $img;
}
?>

<?php

function getTreeDescription($uid, $date, $refer_id) { ?>
    <div class="description-hover">
        <h3>Member Details</h3>
        <div class="nameBox">
            <p>DOJ: <?php echo date("d-m-Y h:i A", strtotime($date)) ?></p>
            <p>Sponsor: <?php echo $refer_id; ?></p>
        </div>
        <table style="float: right;">
            <tbody> 
                <tr> 
                    <td></td>
                    <td>Left</td>
                    <td>Right</td>
                    <td>Total</td>
                </tr>
                <tr>
                    <td>ID</td>
                    <td><?php echo $l = get_count_child_ids($uid, 'L'); ?></td>
                    <td><?php echo $r = get_count_child_ids($uid, 'R'); ?></td>
                    <td><?php echo $l + $r; ?></td>
                </tr>
                <tr>
                    <td>BV</td>
                    <td><?php echo $l = get_child_bv_total($uid, 'L')*1; ?></td>
                    <td><?php echo $r = get_child_bv_total($uid, 'R')*1; ?></td>
                    <td><?php echo $l + $r; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php

function getTree($uid, $__i = 1, $__k = 0) {
    $__k++;
    $rs = my_query("SELECT * FROM user WHERE placement_id = '$uid'");
    $num = my_num_rows($rs);
    $parr = ['L' => 1, 'R' => 2];
    ?>
    <div class="row">
        <?php
        if ($num) {
            while ($row = my_fetch_object($rs)) {
                $p = $row->position;
                $style = ($p == 'R') ? 'pull-right' : '';
                unset($parr[$p]);
                ?>
                <div class="col-xs-6 <?php echo $style; ?>" style="<?php //echo $style; ?>">
                    <div align="center">
                        <div class="user">
                            <a class="tree-img" href="tree_view.php?no=<?php echo $uid; ?>" title=""><br />
                                <?php echo $row->uid;//$row->login_id; ?><br />
                                <img src="images/user_<?php echo get_img_clr($row); ?>.png" />
                                <!--/**********************/-->
                                <?php getTreeDescription($row->uid, $row->datetime, $row->refer_id); ?>
                                <!--/**********************/-->
                            </a>
                        </div>
                        <?php if ($__k < $__i) { ?>
                            <img src="images/line.png" style="width: 67%;" />
                            <?php getTree($row->uid, $__i, $__k); ?>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
        }

        foreach ($parr as $k => $v) {
            $style = ($k == 'R') ? 'pull-right' : '';
            ?>
            <div class="col-xs-6 <?php echo $style; ?>" style="<?php //echo $style; ?>">
                <div align="center">
                    <div class="user">
                        <a href="tree_register.php?placement_id=<?php echo $uid; ?>&&position=<?php echo $k; ?>" target="_blank" title="Add Member"><br />
                            New<br />
                            <img src="images/new_user.png" />
                        </a>
                    </div>
                    <?php /*if ($__k < $__i) { ?>
                        <img src="images/line.png" style="width: 67%;" />
                        <?php getTree(-1, $__i, $__k); ?>
                    <?php }*/ ?>
                </div>
            </div>
            <?php }
        ?>
    </div>
    <?php
}
?>