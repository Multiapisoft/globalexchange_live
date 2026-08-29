<?php include_once '../lib/config.php';
date_default_timezone_set('Asia/Kolkata');

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

$datetime_3 = date('Y-m-d H:i:s', strtotime('0 days', strtotime(date('c'))));
$rs = my_query("SELECT * FROM investments WHERE status = 0 AND datetime <='" . $datetime_3 . "' AND ipid <= 3 AND amount >= 100 AND ipid!=2");


while ($row = my_fetch_object($rs)) {
    // echo "<prer>";
    // print_r($row);
    // echo "</prer>";
    // echo "<br> ================================================ <br>";
    $uid = $row->uid;
    $user = get_user_details($uid);

    $plan = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $row->ipid . "'"));
    $check_today = my_fetch_object(my_query("SELECT * FROM income_growth WHERE iid='" . $row->recid . "' ORDER BY datetime DESC"));
    // echo "Debug: check_today->datetime=" . ($check_today ? $check_today->datetime : 'N/A') . "<br>";
    $true = 1;
    $plan_days = $plan->days;
    $time_floor = floor((strtotime(date('c')) - strtotime($row->datetime)) / (60 * 60 * 24));
    $time_floor2 = floor((strtotime(date('c')) - strtotime($row->datetime)) / (60 * 60));
    $time_floor3 = floor((strtotime(date('c')) - strtotime($row->datetime)) / (60));

    if ($row->ipid != 2 && $check_today && date("Y-m-d", strtotime($check_today->datetime)) == date('Y-m-d') && $time_floor2 < 24) {
        $true = 0;
    }

    if ($row->ipid == 2 && $time_floor3 < 60) {
        $true = 0;
    }

    if ($row->ipid == 2 && ($time_floor2 % $row->hour) > 0) {
        $true = 0;
    }
    // echo "UID: $uid | IPID: $row->ipid | CHECK_TODAY: " . ($check_today ? $check_today->datetime : 'N/A') . " | TRUE: $true<br>";


    // echo "Debug: plan->daily=$plan->daily | ipid=$row->ipid | true=$true | time_floor=$time_floor | days=$row->days<br>";

    if ((($plan->daily == 1 || ((($time_floor % 30) == 0) && $plan->daily == 30) || ((($time_floor % 7) == 0) && $plan->daily == 7)) && $true) || ($row->ipid == 2 && $true)) {
        // echo "Cron Job start...<br/>";
        
        if ($row->days < $plan_days) {
            $days = $row->days + 1;
            // $percentage = $plan->percentage;
            
            // $min_per = $plan->percentage;
            // $max_per = 2.2;
            
            
            
            // $min_per = $plan->percentage;
            // $max_per = $plan->percentage_to;
            
            
            
            $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='" . $uid . "' AND status = 0 AND topup > 0"));
            
            $_ipid = $row->ipid;
            /*if($row->ipid >= 9 && $check >= 500){
                $_ipid = 9;
                }
                elseif($row->ipid >= 8 && $check >= 200){
                    $_ipid = 8;
                }
                elseif($row->ipid >= 7 && $check >= 100){
                    $_ipid = 7;
                    }
                    elseif($row->ipid >= 6 && $check >= 50){
                        $_ipid = 6;
                        }
                        elseif($row->ipid >= 5 && $check >= 30){
                            $_ipid = 5;
                            }
                            elseif($row->ipid >= 4 && $check >= 20){
                                $_ipid = 4;
                                }
                                elseif($row->ipid >= 3 && $check >= 10){
                                    $_ipid = 3;
                                    }
                                    elseif($row->ipid >= 2 && $check >= 5){
                                        $_ipid = 2;
                                        }*/

            $plan = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $_ipid . "'"));
            $min_per = $plan->percentage;
            $max_per = $plan->percentage_to;

            $percentage = round((mt_rand($min_per * 1000, $max_per * 1000) / 1000) / 720, 3);

            $amount = ($row->amount * $percentage) / 100;
            $iamount = $row->amount;
            
            // Capping Limmit  
            // $num_of_rows = my_num_rows(my_query("SELECT * FROM user WHERE refer_id = $uid"));

            // print_r($num_of_rows);
            // die;
            // $query  = "Select * From user where refer_id = $uid";

            // if ($num_of_rows > 0) {
            //     $amount = check_3x($uid, $amount);
            // } else {
            //     $amount = check_2x($uid, $amount);
            // }


            if ($amount > 0) {
                my_query("INSERT INTO `income_growth` (`uid`, `iid`, `days`, `amount`, `datetime`, percentage, iamount) VALUES ('" . $row->uid . "', '" . $row->recid . "', '" . $days . "', '" . $amount . "', '" . date('c') . "', '$percentage', '" . $iamount . "')");
                $_recid = my_insert_id();
                my_query("UPDATE investments SET days = days+1, trade_status = 0 WHERE recid='$row->recid'");
                if ($row->ipid != 2) {
                    my_query("UPDATE user SET wallet=wallet+'" . $amount . "', trade_status = 0 WHERE uid='" . $uid . "'");
                }

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





function incentives($uid, $amount, $pamt)
{
    /*************************/

    $top = get_top_level_uids2($uid, 5);
    
    // echo "<pre>";
    // print_r($top);
    // echo "</pre>";
    // level_roi_income % Set Here 

    // $level_amount = array(0.2, 0.15, 0.1, 0.05, 0.04 0.03, 0.02, 0.02, 0.01, 0.01, 0.05, 0.02, 0.015, 0.015, 0.01, 0.01, 0.01, 0.01, 0.01, 0.005, 0.005, 0.005, 0.005, 0.005, 0.0025, 0.0025, 0.0025, 0.0025, 0.03, 0.0025, 0.0025, 0.0025, 0.0025);
    $level_amount = array(
        0.20,
        0.15,
        0.10,
        0.05,
        0.04,
        0.03,
        0.02,
        0.02,
        0.015,
        0.015,
        0.01,
        0.01,
        0.01,
        0.01,
        0.01,
        0.005,
        0.005,
        0.005,
        0.005,
        0.005,
        0.0025,
        0.0025,
        0.0025,
        0.0025,
        0.03
    );

    $i = 0;
    $level = count($top);
    if ($level > 25) {
        $level = 25;
    }
    echo "Level: $level<br>";
    if ($level > 0) {
        while ($i < $level) {
            $value = $top[$i];

            if ($i < 25) {
                $j = $i;
            } else {
                $j = 25;
            }
            $percentage = ($level_amount[$j]) / 720; // daily percentage
            $new_amount = $percentage * $amount;
            $user2 = get_user_details($value);
            $new_amount = check_3x($value, $new_amount);

            $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='" . $value . "' AND status = 0 AND topup > 0"));

            $_n = $i + 1;
            $_n = 0;

            // if(in_array($uid, array(100, 31753049, 14461162, 71039087))){
            //     $_n = 0;
            // }

            if ($user2->topup > 0 && $new_amount > 0 && ($i == 0 || ($check >= $_n))) {
                my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='" . $value . "'");
                /*if($i==0){
                    my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`) VALUES ('" .$value ."','".$uid."','".$new_amount."','".$pamt."','".date('c')."')");
                }
                else{*/
                my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`, `level`, type) VALUES ('" . $value . "','" . $uid . "','" . $new_amount . "','" . $pamt . "','" . date('c') . "','" . ($i + 1) . "', 2)");
                //}

                // $rs = my_query("SELECT uid, package, reward, topup_datetime FROM user WHERE status = 0 AND refer_id = '".$value."'");
                // while ($row = my_fetch_object($rs)) {
                //     if($row->package >= 1){
                //         upline($row, $value, $pamt, $new_amount, 1);
                //     }
                // }
            }
            $i++;
        }
    }
    /*************************/
}

/* get top level uids */
function get_top_level_uids2($uid, $level = 0, $arr = array())
{
    global $link;
    $result = my_query("SELECT refer_id FROM user WHERE uid = '$uid'");
    if (count($arr) == $level && $level != 0) {
        return $arr;
    } elseif ($uid == 100) {
        return $arr;
    }
    if (my_num_rows($result) > 0) {
        $data = my_fetch_array($result);
        $arr[count($arr)] = $data[0];
        return get_top_level_uids2($data[0], $level, $arr);
    } else {
        return $arr;
    }
}

function get_bamt($uid)
{
    global $link;
    $amount = 0;
    $childs = get_single_dimensional(get_child_levels($uid));
    if (!empty($childs)) {
        $uid_in = implode(" , ", $childs);
        $amount = my_fetch_object(my_query("SELECT SUM(amount) as amount FROM investments WHERE uid IN ( $uid_in )"))->amount;
    }
    return $amount;
}

function upline($row, $value, $pamt, $amount, $level)
{
    $datetime_30 = date('Y-m-d H:i:s', strtotime('+30 days', strtotime($row->topup_datetime)));
    // $datetime_30 = date('Y-m-d H:i:s');
    $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '" . $row->uid . "' AND status = 0 AND topup > 0 AND topup_datetime <= '" . $datetime_30 . "'"));
    $n = ($level == 1) ? 8 : ($level == 2 ? 25 : 100);
    if ($check >= $n) {
        $per = ($level == 1) ? 0.02 : ($level == 2 ? 0.02 : 0.01);
        $new_amount = $amount * $per;
        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`, `level`, `type`) VALUES ('" . $row->uid . "','" . $value . "','" . $new_amount . "','" . $pamt . "','" . date('c') . "','" . $level . "', 1)");
    }
}

echo "<br/> Closing complete. Please close this browser.";
