<?php include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

my_query("UPDATE `user` SET `teamb`=0,`teamc`=0, teamb2=0 WHERE 1");

$result = my_query("SELECT uid, topup, reward, package FROM user WHERE status = 0 ORDER BY datetime DESC");
while ($row = my_fetch_object($result)) {
    $uid = $row->uid;
    if ($row->topup > 0) {
        $tb = get_sum('investments', 'amount', "uid = '".$uid."' AND ipid <= 8");
        $tb2 = get_sum('investments', 'amount', "uid = '".$uid."' AND ipid = 4");
        my_query("UPDATE user SET teamb2 = teamb2 + '" . $tb2 . "' WHERE uid = '$uid'");
        // $fund_amounts = get_sum('fund_transfer', 'amount', "uid = '" . $uid . "' ");
        // $diposite_amounts = get_sum('deposit_block', 'amount', "uid = '" . $uid . "' ");
        // $tb = ($fund_amounts ?? 0) + ($diposite_amounts ?? 0);
        $top = get_top_level_uids2($uid, 25000);
        //$top = get_top_level_uids($uid, 25000);
        foreach ($top as $v) {
            my_query("UPDATE user SET teamb = teamb + '" . $tb . "', teamb2 = teamb2 + '" . $tb2 . "', teamc = teamc + 1 WHERE uid = '$v'");
        }

        /*$check_d = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='".$uid."' AND status = 0 AND topup > 0"));
        $reward = $row->reward;
            
        if($row->package >= 9 && $check_d >= 500){
            $reward = 9;
        }
        elseif($row->package >= 8 && $check_d >= 200){
            $reward = 8;
        }
        elseif($row->package >= 7 && $check_d >= 100){
            $reward = 7;
        }
        elseif($row->package >= 6 && $check_d >= 50){
            $reward = 6;
        }
        elseif($row->package >= 5 && $check_d >= 30){
            $reward = 5;
        }
        elseif($row->package >= 4 && $check_d >= 20){
            $reward = 4;
        }
        elseif($row->package >= 3 && $check_d >= 10){
            $reward = 3;
        }
        elseif($row->package >= 2 && $check_d >= 5){
            $reward = 2;
        }
        elseif($row->package >= 1 && $check_d >= 0){
            $reward = 1;
        }
        my_query("UPDATE user SET reward = '".$reward."' WHERE uid = '$uid'");*/
    }

    // $l = get_child_bv_total2($uid, 'L');
    // $r = get_child_bv_total2($uid, 'R');
    // my_query("UPDATE user SET tbl = '".$l."', tbr = '".$r."' WHERE uid = '$uid'");

    /*if($reward >= 3){
        $top = get_top_level_uids2($uid, 3);
        
        $level_amount = array(
            3 => array(50, 15, 5),
            4 => array(200, 120, 20),
            5 => array(600, 180, 60),
            6 => array(900, 300, 90),
            7 => array(1200, 400, 120),
            8 => array(1800, 600, 150),
            9 => array(2000, 700, 200)
        );
    
        $i = 0;
        $level = count($top);
        if($level>3){$level=3;}
        if($level>0){
            while($i<$level){
                $value = $top[$i];
                $user2 = get_user_details($value);
                $_reward = 3;
                while($_reward <= $reward){
                    if($user2->reward >= $_reward){
                        $new_amount = $level_amount[$_reward][$i];
                        $new_amount = check_2x($value, $new_amount);
                        if($new_amount > 0){
                            $check = my_num_rows(my_query("SELECT uid FROM income_level WHERE uid =  '".$value."' AND from_uid = '".$uid."' AND type = 0 AND level = '".($i+1)."' AND pool = '".$_reward."'"));
                            if(false){
                                my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");
                                my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, ipid, iamount, pool, type) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."', '0', '0','".($_reward)."', 0)");
                            }
                        }
                    }
                    $_reward++;
                }
                $i++;
            }
        }
    }*/
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
echo "<br/> Closing complete. Please close this browser.";
