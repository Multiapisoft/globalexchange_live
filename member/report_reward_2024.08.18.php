<?php $type = 0;
$title = "Royalty";
include_once 'header.php';
$marr = array(0, 5000, 20000, 70000, 170000, 670000, 2670000, 2670000);
$marr = array(0, 10000, 40000, 140000, 640000, 2640000, 7640000, 7640000);
$amtarr = array(0, 2, 5, 10, 50, 1500, 1500);
$reward = $user->reward;
$reward = 0;
$i=0;
$reward_arr = get_reward();
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Target SLB 60%</th>
                                <th>Target 2nd LB 30%</th>
                                <th>Target OLB 10%</th>
                                <th>Achived SLB</th>
                                <th>Achived 2nd LB</th>
                                <th>Achived OLB</th>
                                <th>Reward Achived</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($i < 6){$i++;
                                $self = $user->topup;
                                $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 0,1"))->amount;
                                $max = ($max) ? $max*1 : 0;
                                $max2 = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 1,1"))->amount;
                                $max2 = ($max2) ? $max2*1 : 0;
                                $max3 = $user->teamb - $max - $max2;
                                $max3 = ($max3 > 0) ? $max3*1 : 0;
                                $mamt = $marr[$i]-$marr[$i-1];
        
                                if($reward < $i && ($marr[$i]*0.6) <= $max && ($marr[$i]*0.3) <= $max2 && ($marr[$i]*0.1) <= $max3){
                                    $reward++;
                                }?>
                            <tr>
                                <td><?php echo $reward_arr[$i];?></td>
                                <td><?php echo $mamt*0.6;?></td>
                                <td><?php echo $mamt*0.3;?></td>
                                <td><?php echo $mamt*0.1;?></td>
                                <td><?php echo ($marr[$i]*0.6 <= $max) ? $mamt*0.6 : ($max-($marr[$i-1] * 0.6) > 0 ? $max-($marr[$i-1] * 0.6) : 0);?></td>
                                <td><?php echo ($marr[$i]*0.3 <= $max2) ? $mamt*0.3 : ($max2-($marr[$i-1] * 0.3) > 0 ? $max2-($marr[$i-1] * 0.3) : 0);?></td>
                                <td><?php echo ($marr[$i]*0.1 <= $max3) ? $mamt*0.1 : ($max3-($marr[$i-1] * 0.1) > 0 ? $max3-($marr[$i-1] * 0.1) : 0);?></td>
                                <td><?php echo ($reward >= $i) ? 'Yes' : 'No';?></td>
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