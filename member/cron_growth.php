<?php
include_once '../lib/config.php';
date_default_timezone_set('Asia/Kolkata');
if (SITE_G_S_DATE == date('Y-m-d')) {
    // die;
}
$day = date('l');
if ($day == 'Sunday' || $day == 'Saturday') {
    die;
}
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(-1);

$datetime_3 = date('Y-m-d H:i:s', strtotime('1 days', strtotime(date('c'))));
// Trading packages only (Silver/Gold). Bot Activation (ipid=1) generates no ROI.
$rs = my_query("SELECT * FROM investments WHERE status = 0 AND datetime<='" . $datetime_3 . "' AND ipid IN (2,3) AND amount >= 100");
while ($row = my_fetch_object($rs)) {
    $uid = $row->uid;
    $user = get_user_details($uid);

    $plan = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . (int)$row->ipid . "'"));
    if (!$plan) {
        continue;
    }
    $plan_days = $plan->days;
    $datetime_30 = date('Y-m-d H:i:s', strtotime('+30 days', strtotime($user->datetime)));
    $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '" . $row->uid . "' AND status = 0 AND topup > 0 AND topup_datetime <= '" . $datetime_30 . "'"));
    $is_booster = ($check >= 5) ? 1 : 0;

    $time_floor = floor((strtotime(date('c')) - strtotime($row->datetime)) / (60 * 60 * 24));
    $is_daily_payout = ($plan->daily == 0 || $plan->daily == 1) || ((($time_floor % 30) == 0) && $plan->daily == 30) || ((($time_floor % 7) == 0) && $plan->daily == 7) || ((int)$row->ipid == 2 && (int)$row->trade_status == 1);
    $is_self_trade_activated = ((int)$row->ipid != 2) || ((int)$row->trade_status == 1);
    if ($is_daily_payout && $is_self_trade_activated) {
        if ($row->days < $plan_days) {
            $days = $row->days + 1;
            $min_per = $plan->percentage;
            $max_per = $plan->percentage_to;

            // Monthly ROI paid as daily slice (Monthly% / 30)
            $percentage = round((mt_rand($min_per * 1000, $max_per * 1000) / 1000) / 30, 3);
            $iamount = ($row->amount);

            $amount = ($iamount * $percentage) / 100;
            $amount = check_capping($uid, $amount);
            if ($amount > 0) {
                my_query("INSERT INTO `income_growth` (`uid`, `iid`, `days`, `amount`, `datetime`, percentage, iamount, is_booster) VALUES ('" . $row->uid . "', '" . $row->recid . "', '" . $days . "', '" . $amount . "', '" . date('c') . "', '$percentage', '" . $iamount . "', '".$is_booster."')");
                $_recid = my_insert_id();
                my_query("UPDATE investments SET days = days+1, trade_status = 0 WHERE recid='$row->recid'");
                my_query("UPDATE user SET wallet= wallet+'$amount' WHERE uid='" . $uid . "'");
                if ((int)$row->ipid == 2) {
                    my_query("UPDATE user SET trade_status = 0 WHERE uid='" . $uid . "'");
                    my_query("UPDATE investments SET trade_status = 0 WHERE uid='" . $uid . "' AND ipid = 2");
                }
                // Level ROI from eligible Trading ROI only
                incentives($uid, $amount, $iamount);
            } else {
                // Cap reached — stop further ROI on this investment
                my_query("UPDATE investments SET status = 1 WHERE recid='$row->recid'");
            }
        } else {
            my_query("UPDATE investments SET status = 1 WHERE recid='$row->recid'");
        }
    }
}

function incentives($uid, $amount, $pamt)
{
    /*************************/
    $top = get_top_level_uids2($uid, 10);

    // Business Plan Level ROI % of downline Trading ROI
    $level_amount = [
        0.20,   // L1
        0.10,   // L2
        0.10,   // L3
        0.05,   // L4
        0.05,   // L5
        0.025,  // L6
        0.025,  // L7
        0.0125, // L8
        0.0125, // L9
        0.0125, // L10
    ];

    $i = 0;
    $level = count($top);
    if ($level > 10) {
        $level = 10;
    }
    if ($level > 0) {
        while ($i < $level) {
            $value = $top[$i];
            $percentage = $level_amount[$i];
            $new_amount = $percentage * $amount;
            $user2 = get_user_details($value);
            $new_amount = check_capping($value, $new_amount);

            $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='" . $value . "' AND status = 0 AND topup > 0"));
            $level_no = $i + 1;
            $required_directs = get_level_roi_required_directs($level_no);

            // Auto-verify Direct qualification before Level ROI
            if ($user2->topup > 0 && $new_amount > 0 && ($check >= $required_directs)) {
                my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='" . $value . "'");
                my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`, `level`, `type`) VALUES ('" . $value . "','" . $uid . "','" . $new_amount . "','" . $pamt . "','" . date('c') . "','" . $level_no . "', 2)");
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
        $uid_in = implode(' , ', $childs);
        $amount = my_fetch_object(my_query("SELECT SUM(amount) as amount FROM investments WHERE uid IN ( $uid_in )"))->amount;
    }
    return $amount;
}

function upline($row, $value, $pamt, $amount, $level)
{
    $datetime_30 = date('Y-m-d H:i:s', strtotime('+30 days', strtotime($row->topup_datetime)));
    $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '" . $row->uid . "' AND status = 0 AND topup > 0 AND topup_datetime <= '" . $datetime_30 . "'"));
    $n = ($level == 1) ? 8 : ($level == 2 ? 25 : 100);
    if ($check >= $n) {
        $per = ($level == 1) ? 0.02 : ($level == 2 ? 0.02 : 0.01);
        $new_amount = $amount * $per;
        my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`, `level`, `type`) VALUES ('" . $row->uid . "','" . $value . "','" . $new_amount . "','" . $pamt . "','" . date('c') . "','" . $level . "', 1)");
    }
}

echo '<br/> Closing complete. Please close this browser.';
