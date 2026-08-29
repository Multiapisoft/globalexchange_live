<?php
$title = 'Matching Income';
include_once 'header.php';
$query = 'SELECT b.amount, b.datetime, b.pair_left, b.pair_right, b.matching, b.left_carry, b.right_carry, b.current_left, b.current_right, b.type, b.self_bv FROM income_binary as b'
    . " WHERE b.uid='" . $uid . "' AND b.type=0"
    . ' ORDER BY b.datetime DESC';
$result = my_query($query);
$i = 0;
?>

<style>
    /* Dark theme for Matching Income table & panel only */
    .panel-bd,
    .panel-bd .panel,
    .panel-bd .panel-body {
        background-color: #1e2329 !important;
        color: #eaecef !important;
        border-color: #2c3137 !important;
    }

    #dataTableExample1 {
        background-color: #1e2329 !important;
        color: #eaecef !important;
        border-color: #2c3137 !important;
    }

    #dataTableExample1 thead {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #ffffff;
    }

    #dataTableExample1 thead th {
        border-bottom: 1px solid #2c3137;
    }

    #dataTableExample1 tbody tr {
        border-bottom: 1px solid #2c3137;
        background-color: #0b0e11;
    }

    #dataTableExample1 tbody tr:nth-child(even) {
        background-color: #0b0e11;
    }

    #dataTableExample1 tbody tr:hover {
        background-color: #2b3139 !important;
        color: #ffffff !important;
    }

    #dataTableExample1 tbody tr.active {
        background-color: #2b3139 !important;
        color: #ffffff !important;
    }

    #dataTableExample1 tbody tr.active td {
        color: #ffffff !important;
        background-color: #2b3139 !important;
    }
</style>

<div class=row>
    <!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-3">
            <h2><span class=count-number> <?php echo get_child_pbv_current_inv($uid, 'L') * 1; ?></span></h2>
            <div class=small>Current Left B</div>
            <i class="ti-world statistic_icon"></i>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number> <?php echo get_child_pbv_current_inv($uid, 'R') * 1; ?></span></h2>
            <div class=small>Current Right B</div>
            <i class="ti-world statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div> -->
</div>
<style>
    .panel-bd{
        margin-right: 80px !important;
    }
    @media (max-width: 768px) {
        .panel-bd{
            margin-right: 0px !important;
        }
        .row {
        margin-left: -10px;
        margin-right: -160px;
    }
    }
</style>
<div class="row">
    <div class="col-sm-12 mx-5 mx-sm-0" style="margin-right: 150px !important;">
        <div class="panel panel-bd">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Left</th>
                                <th>Right</th>
                                <th>Matching</th>
                                <th>Left Carry</th>
                                <th>Right Carry</th>
                                <th><?php echo SITE_CURRENCY; ?></th>
                                <!--<th>Current Left</th>
                                <th>Current Right</th>-->
                                <!--<th>Self</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_object($result)) {
                                $i++; ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo date('d M, Y h:i A', strtotime($row->datetime)); ?></td>
                                <td><?php echo $row->pair_left * 1; ?></td>
                                <td><?php echo $row->pair_right * 1; ?></td>
                                <td><?php echo $row->matching * 1; ?></td>
                                <td><?php echo $row->left_carry * 1; ?></td>
                                <td><?php echo $row->right_carry * 1; ?></td>
                                <td><?php echo $row->amount * 1; ?></td>
                                <?php  /*<td><?php echo $row->current_left*1;?></td>
                                 <td><?php echo $row->current_right*1;?></td>*/
                                ?>
                                <?php /* <td><?php echo $row->self_bv*1;?></td> */ ?>
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