<?php include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
//$marr = array(0, 500, 1500, 3000, 6000, 12000, 24000, 48000, 96000, 192000, 384000, 7680000, 1536000, 3072000, 6144000, 12288000);
$marr = array(0, 500, 2000, 5000, 11000, 23000, 47000, 95000, 191000, 383000, 767000, 1535000, 3071000, 6143000, 12287000, 24575000);
$amtarr = array(0, 15, 30, 60, 120, 200, 350, 700, 1500, 2800, 3000, 6000, 10000, 20000, 40000, 100000, 100000, 100000);
$weeks_arr = array(0, 42, 48, 96, 144, 244, 244);
$date = date('Y-m-01');

$result = mysqli_query($link, "SELECT * FROM user WHERE topup > 0 AND reward <= 15");
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $reward = $row->reward;
    
    $i = $reward;
    while($i < 15){$i++;
        $self = $row->topup;
        $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 0,1"))->amount;
        $max = ($max) ? $max : 0;
        $max2 = $row->teamb - $max;
        $max2 = ($max2 > 0) ? $max2 : 0;
        
        if($reward < $i && ($marr[$i]) <= $max && ($marr[$i]) <= $max2){
            $reward++;
            mysqli_query($link, "UPDATE user SET reward = '".$reward."' WHERE uid = '$uid'");
            $new_amount = $amtarr[$i];
            //$new_amount = 0;
            royalty_insert($uid, $new_amount, $i, 1);
        }
    }
}

$tamt = get_sum('investments', 'amount', "statusc = 0");
$tamtp = array(0, 0, 0, 0, 0, 0, 0, 0.02, 0.01, 0.005, 0.005, 0.005, 0.005, 0.005, 0.005, 0.01, 0.005, 0.005, 0.005, 0.005, 0.005);

$i = 6;
while($i < 15){$i++;
    $tid = my_num_rows(my_query("SELECT uid FROM user WHERE status = 0 AND reward >= '".$i."'"));
    
    if($tamt > 0 && $tid > 0){
        $rs = my_query("SELECT * FROM user WHERE status = 0 AND reward >= '".$i."'");
        while ($row = my_fetch_object($rs)) {
            $uid = $row->uid;
            $tamtb = $tamt * $tamtp[$i];
            $new_amount = round($tamtb/$tid, 4);
            if($new_amount>0){
                royalty_insert($uid, $new_amount, $i, 0, $tid, $tamt);
            }
        }
    }
}

function royalty_insert($uid, $new_amount, $l, $type = 0, $n = 0, $tamt = 0){
    $new_amount = check_3x($uid, $new_amount);
    global $weeks_arr;
    
    if($type == 1 && $new_amount > 0){
        $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='".$uid."' AND type = '".$type."' AND level = '".$l."'"));
        if(!$check){
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$uid."'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`) VALUES ('".$uid ."', '".$new_amount."', '".date('c')."', '".$l."', '".$type."')");
        }
    }
    elseif($type == 0 && $new_amount > 0){
        $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='".$uid."' AND type = '".$type."' AND level = '".$l."'"));
        //$weeks = $weeks_arr[$l];
        $weeks = 0;
        //if($check < 200){
            $days = $check+1;
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$uid."'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`, tamt, tid, days) VALUES ('".$uid ."', '".$new_amount."', '".date('c')."', '".$l."', '".$type."', '".($tamt)."', '".$n."', '".$days."')");
        //}
    }
}

function get_qlfy($uid, $reward){
    $amount = 0;
    $childs = get_single_dimensional(get_child_levels($uid));
    if (!empty($childs)) {
        $uid_in = implode(" , ", $childs);
        $amount = my_num_rows(my_query("SELECT uid FROM user WHERE uid IN ( $uid_in ) AND status = 0 AND reward = '".$reward."'"));
    }
    return $amount;
}

//if($date == date('Y-m-d')){
    //my_query("UPDATE investments SET statusc=1 WHERE statusc=0");
//}
echo "<br/> Closing complete. Please close this browser.";
?>