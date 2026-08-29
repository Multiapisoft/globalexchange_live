<?php include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
$date = date('Y-m-d');

$tb_arr = array(0, 1000, 5000, 10000, 25000, 50000, 100000, 250000, 500000, 1000000, 2500000, 5000000, 10000000);
$amt_arr = array(0, 50, 150, 250, 500, 1500, 2500, 5000, 8000, 15000, 25000, 50000, 100000);

$fm_rnk_arr = array(0, 1, 2, 3);
$fm_time_arr = array(0, 30, 60, 90);
$fm_m_arr = array(0, 6, 12, 18);
$fm_amt_arr = array(0, 100, 300, 500);

$c_rnk_arr = array(0, 3, 5, 7, 8);
$c_m_arr = array(0, 30, 100, 200, 250);
$c_amt_arr = array(0, 4, 3, 2.5, 1.5);

//$result = mysqli_query($link, "SELECT * FROM user WHERE topup > 0 AND reward <= 15");
$result = mysqli_query($link, "
    SELECT DISTINCT u.* 
    FROM user u
    INNER JOIN investments i ON i.uid = u.uid
    WHERE u.topup > 0 
      AND i.ipid <= 4 AND u.reward < 12
");
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $reward = $row->reward;
    $time_floor = floor((strtotime(date('c')) - strtotime($row->datetime)) / (60 * 60 * 24));

    $i = $reward;
    while ($i < 12) {
        $i++;
        $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb) DESC LIMIT 0,1"))->amount;
        $max = ($max) ? $max : 0;
        //$max2 = @mysqli_fetch_object(mysqli_query($link, "SELECT SUM(teamb2) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb2) DESC LIMIT 1,9999999999"))->amount;
        $max2 = $row->teamb;
        $max2 = ($max2) ? $max2 - $max : 0;

        echo $uid.'===='.$max.'====='.$max2.'<br>';
        
        if ($reward < $i && ($tb_arr[$i] * 0.6) <= $max && ($tb_arr[$i] * 0.4) <= $max2) {
            $reward++;
            mysqli_query($link, "UPDATE user SET reward = '" . $reward . "' WHERE uid = '$uid'");
            $new_amount = $amt_arr[$reward];
            royalty_insert($uid, $new_amount, $reward, 0);
            if(isset($fm_time_arr[$i]) && $time_floor <= $fm_time_arr[$i]){
                if($i == 1){
                    mysqli_query($link, "UPDATE user SET royalty = 1 WHERE uid = '$uid'");
                    $row->royalty = 1;
                }
                if($i == 2){
                    mysqli_query($link, "UPDATE user SET royalty2 = 1 WHERE uid = '$uid'");
                    $row->royalty2 = 1;
                }
                if($i == 3){
                    mysqli_query($link, "UPDATE user SET royalty3 = 1 WHERE uid = '$uid'");
                    $row->royalty3 = 1;
                }
            }
        } else {
            break;
        }
    }

    if($date == date('Y-m-d')){
        $matching = get_sum('income_binary', 'matching', "statusc = 0 AND uid = '".$uid."'");
        $i = 0;
        while ($i < 3) {$i++;
            $_royalty = 0;
            if($i == 1 && $row->royalty){
                $_royalty = 1;
            }
            else if($i == 2 && $row->royalty2){
                $_royalty = 1;
            }
            else if($i == 3 && $row->royalty3){
                $_royalty = 1;
            }

            if($_royalty && $matching >= $fm_m_arr[$i]){
                $new_amount = $fm_amt_arr[$i];
                royalty_insert($uid, $new_amount, $i, 1);
            }
        }
    }
}

if($date == date('Y-m-d')){
    $i = 0;
    $tamt = get_sum('investments', 'amount', "statusc = 0 AND ipid = 4");
    while ($i < 4 && $tamt) {
        $i++;
        $tid = my_num_rows(my_query("SELECT uid FROM user WHERE status = 0 AND topup > 0 AND reward >= '".$c_rnk_arr[$i]."' AND uid IN (SELECT uid FROM income_binary WHERE matching >= '".$c_m_arr[$i]."' AND statusc = 0)"));

        if ($tamt > 0 && $tid > 0) {
            $rs = my_query("SELECT uid FROM user WHERE status = 0 AND topup > 0 AND reward >= '".$c_rnk_arr[$i]."' AND uid IN (SELECT uid FROM income_binary WHERE matching >= '".$c_m_arr[$i]."' AND statusc = 0)");
            while ($row = my_fetch_object($rs)) {
                $uid = $row->uid;
                $tamtb = $tamt * $c_amt_arr[$i] / 100;
                $new_amount = round($tamtb / $tid, 4);
                if ($new_amount > 0) {
                    royalty_insert($uid, $new_amount, $i, 2, $tid, $tamt);
                }
            }
        }
    }
}

function royalty_insert($uid, $new_amount, $l, $type = 0, $n = 0, $tamt = 0)
{
    // $new_amount = check_2x($uid, $new_amount);

    if ($type == 2 && $new_amount > 0) {
        $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='".$uid."' AND type = '".$type."' AND level = '".$l."'"));
        // if(!$check){
            $days = $check + 1;
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='" . $uid . "'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`, tamt, tid, days) VALUES ('" . $uid . "', '" . $new_amount . "', '" . date('c') . "', '" . $l . "', '" . $type . "', '" . ($tamt) . "', '" . $n . "', '" . $days . "')");
        // }
    } elseif ($type == 1 && $new_amount > 0) {
        $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='" . $uid . "' AND type = '" . $type . "' AND level = '" . $l . "'"));
        if ($check < 3) {
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='" . $uid . "'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`) VALUES ('" . $uid . "', '" . $new_amount . "', '" . date('c') . "', '" . $l . "', '" . $type . "')");
        }
    } elseif ($type == 0 && $new_amount > 0) {
        $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='" . $uid . "' AND type = '" . $type . "' AND level = '" . $l . "'"));
        if (!$check) {
            $days = $check + 1;
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='" . $uid . "'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`, tamt, tid, days) VALUES ('" . $uid . "', '" . $new_amount . "', '" . date('c') . "', '" . $l . "', '" . $type . "', '" . ($tamt) . "', '" . $n . "', '" . $days . "')");
        }
    }
}

if($date == date('Y-m-d')){
    // my_query("UPDATE investments SET statusc=1 WHERE statusc=0");
    // my_query("UPDATE income_binary SET statusc=1 WHERE statusc=0");
}
echo "<br/> Closing complete. Please close this browser.";
