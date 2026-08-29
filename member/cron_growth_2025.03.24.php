<?php include_once '../lib/config.php';
if (SITE_G_S_DATE == date('Y-m-d')) {
    die;
}
$day = date('l');
if ($day == 'Sunday' || $day == 'Saturday') {
    //die;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

$datetime_3 = date('Y-m-d H:i:s', strtotime('-0 days', strtotime(date('c'))));
$rs = my_query("SELECT * FROM investments WHERE status = 0 AND datetime<='" . $datetime_3 . "' AND ipid <= 3");
while ($row = my_fetch_object($rs)) {
    $uid = $row->uid;
    $user = get_user_details($uid);

    $plan = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $row->ipid . "'"));
    $plan_days = $plan->days;

    $time_floor = floor((strtotime(date('c')) - strtotime($row->datetime)) / (60 * 60 * 24));
    if ($plan->daily == 1 || ((($time_floor % 30) == 0) && $plan->daily == 30) || ((($time_floor % 7) == 0) && $plan->daily == 7)) {
        if ($row->days < $plan_days) {
            $days = $row->days + 1;
            
            $percentage = $plan->percentage;
            $amount = ($row->amount2 * $percentage) / 100;
            $iamount = $row->amount2;
            $amount = check_3x($uid, $amount);
            if ($amount > 0) {
                my_query("INSERT INTO `income_growth` (`uid`, `iid`, `days`, `amount`, `datetime`, percentage, iamount) VALUES ('" . $row->uid . "', '" . $row->recid . "', '" . $days . "', '" . $amount . "', '" . date('c') . "', '$percentage', '".$iamount."')");
                $_recid = my_insert_id();
                my_query("UPDATE investments SET days = days+1 WHERE recid='$row->recid'");
                my_query("UPDATE user SET wallet=wallet+'" . $amount . "' WHERE uid='" . $uid . "'");
                /*************************/
                
                incentives($uid, $amount, $iamount);
                
                /*$refer = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $uid . "'"));
                $refer_id = $refer->refer_id;
                $refer2 = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $refer_id . "'"));
                //$per = 5/30;
                $per = 5;
                if($refer2->package >= 3){
                    //$per = (4+$refer2->package)/30;
                }
                $new_amount = $amount * $per / 100;
                my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$refer_id."'");
                my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, type) VALUES ('" .$refer_id ."','".$uid."','".$new_amount."','".date('c')."', 1)");
                */
                /*************************/
            }
        } else {
            my_query("UPDATE investments SET status = 1 WHERE recid='$row->recid'");
        }
    }
}

function incentives($uid, $amount, $pamt){
    /*************************/
    $top = get_top_level_uids2($uid, 20);

    $level_amount = array(0.25, 0.15, 0.08, 0.04, 0.04, 0.02, 0.02, 0.02, 0.02, 0.02, 0.01, 0.01, 0.01, 0.01, 0.01, 0.005, 0.005, 0.005, 0.005, 0.005, 0.005, 0.005, 0.005);

    $i = 0;
    $level = count($top);
    if($level>20){$level=20;}
    if($level>0){
        while($i<$level){
            $value = $top[$i];
            if($i<16){$j=$i;}else{$j=16;}
            $percentage = $level_amount[$j];
            $new_amount = $percentage * $amount;
            $user2 = get_user_details($value);
            $new_amount = check_3x($value, $new_amount);
            
            $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='".$value."' AND status = 0 AND topup > 0"));
            $db = @my_fetch_object(my_query("SELECT SUM(topup) AS amount FROM user WHERE refer_id = '".$value."' AND status = 0"))->amount;
            
            $_n = 1;
            
            if($i>=1){
                $_n = ($i+1);
            }
            if($i>=6){
                $_n++;
            }
            if($i>=9){
                $_n++;
            }
            if($i>=12){
                $_n++;
            }
            if($i>=15){
                $_n++;
            }
            if($i>=18){
                $_n++;
            }
            
            //if($user2->topup > 0 && $new_amount > 0 && ($i == 0 || ($check >= 2 && $db >= 500))){
            if($user2->topup > 0 && $new_amount > 0 && ($i == 0 || $check >= 10 || ($check >= $_n))){
                my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");

                if($i==0){
                    my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`) VALUES ('" .$value ."','".$uid."','".$new_amount."','".$pamt."','".date('c')."')");
                }
                else{
                    my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`, `level`, type) VALUES ('" .$value ."','".$uid."','".$new_amount."','".$pamt."','".date('c')."','".($i)."', 2)");
                }
            }
            $i++;
        }
    }
    /*************************/
}

/* get top level uids */
function get_top_level_uids2($uid, $level=0, $arr=array()){
    global $link;
    $result = my_query("SELECT refer_id FROM user WHERE uid = '$uid'");
    if(count($arr)==$level && $level!=0){
        return $arr;
    }elseif($uid==100){
        return $arr;
    }
    if(my_num_rows($result)>0){
        $data = my_fetch_array($result);
        $arr[count($arr)] = $data[0];
        return get_top_level_uids2($data[0], $level, $arr);
    }else {
        return $arr;
    }
}

function get_bamt($uid) {
    global $link;
    $amount = 0;
    $childs = get_single_dimensional(get_child_levels($uid));
    if (!empty($childs)) {
        $uid_in = implode(" , ", $childs);
        $amount = my_fetch_object(my_query("SELECT SUM(amount) as amount FROM investments WHERE uid IN ( $uid_in )"))->amount;
    }
    return $amount;
}

echo "<br/> Closing complete. Please close this browser.";
?>