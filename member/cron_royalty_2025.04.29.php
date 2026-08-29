<?php include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
$marr = array(0, 5000, 15000, 40000, 90000, 190000, 440000, 940000, 1940000, 4440000, 9440000);
$amtarr = array(0, 15, 30, 50, 100, 150, 500, 1500, 3000, 5000, 10000);
$amtarr2 = array(0, 50, 250, 500, 700, 1000, 2000, 5000, 11000, 50000, 100000);
$weeks_arr = array(0, 42, 48, 96, 144, 244, 244);
$team_arr = array(0, 12, 80, 250, 1000, 5000, 7500);

$date = date('Y-m-15');

$result = mysqli_query($link, "SELECT * FROM user WHERE topup > 0 AND reward <= 10");
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $reward = $row->reward;
    
    $i = $reward;
    while($i < 10){$i++;
        $self = $row->topup;
        $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 0,1"))->amount;
        $max = ($max) ? $max : 0;
        // $max2 = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 1,1"))->amount;
        // $max2 = ($max2) ? $max2 : 0;
        $max2 = 0;
        $max3 = $row->teamb - $max - $max2;
        $max3 = ($max3 > 0) ? $max3 : 0;
        
        // echo "$reward .'<'. $i .'&&'. $team_arr[$i] .'>='. $row->teamc .'&&'. ($marr[$i]*0.6) .'<='. $max && ($marr[$i]*0.3) .'<='. $max2 && ($marr[$i]*0.1) .'<='. $max3";
        // echo '<br>';
        
        //if($reward < $i && $team_arr[$i] <= $row->teamc && ($marr[$i]*0.6) <= $max && ($marr[$i]*0.3) <= $max2 && ($marr[$i]*0.1) <= $max3){
        if($reward < $i && ($marr[$i]*0.5) <= $max && ($marr[$i]*0.0) <= $max2 && ($marr[$i]*0.5) <= $max3){
            $reward++;
            mysqli_query($link, "UPDATE user SET reward = '".$reward."' WHERE uid = '$uid'");
            $new_amount = $amtarr2[$reward];
            royalty_insert($uid, $new_amount, $reward, 1);
        }
        else{
            break;
        }
    }
    if($reward && $date == date('Y-m-d')){
    //if($reward){
        $new_amount = $amtarr[$reward];
        //$new_amount = 0;
        royalty_insert($uid, $new_amount, $reward, 0);
    }
}

/*$tamt = get_sum('investments', 'amount', "statusc = 0");
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
}*/

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
        if($check < 10){
            $days = $check+1;
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$uid."'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`, tamt, tid, days) VALUES ('".$uid ."', '".$new_amount."', '".date('c')."', '".$l."', '".$type."', '".($tamt)."', '".$n."', '".$days."')");
        }
    }
}

//if($date == date('Y-m-d')){
    //my_query("UPDATE investments SET statusc=1 WHERE statusc=0");
//}
echo "<br/> Closing complete. Please close this browser.";
?>