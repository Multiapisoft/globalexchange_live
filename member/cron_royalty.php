<?php
/**
 * Reward Income (type 0) & Royalty Income (type 2) cron.
 * IMPORTANT: Run cron_tb.php FIRST and let it complete. This cron uses user.teamb;
 * if cron_tb has just zeroed teamb (SET teamb=0) and not yet repopulated, reward/royalty conditions will fail.
 */
include_once '../lib/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
$date = date('Y-m-d');

$day = date('l');
if ($day == 'Sunday' || $day == 'Saturday') {
    die;
}

// $tb_arr = array(0, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000, 500000, 1000000, 3000000, 10000000, 20000000, 30000000, 50000000);
$tb_arr = array(0, 500, 1500, 3500, 8500, 18500, 38500, 88500, 188500, 688500, 1688500, 4688500, 14688500, 34688500, 54688500, 104688500);
// $tb_arr = array(0, 500, 1500, 3500, 5000, 10000, 20000, 50000, 100000, 500000, 1000000, 3000000, 10000000, 20000000, 30000000, 50000000);
$amt_arr = array(0, 50, 100, 200, 500, 1000, 2000, 5000, 10000, 50000, 100000, 300000, 1000000, 2000000, 3000000, 5000000);

// Rank Royalty: Team Business milestones (50% strong + 50% weak) & Daily Royalty per plan
$tb_arr2 = array(0, 15000, 30000, 60000, 200000, 500000, 1000000, 5000000, 10000000, 20000000, 50000000);  // SMART to CROWN AMBASSADOR
$amt_arr2 = array(0, 18, 36, 72, 240, 600, 1200, 6000, 12000, 24000, 60000);  // Daily Royalty $

$rank_names = array(0 => '-', 1 => 'SMART', 2 => 'PLAYER', 3 => 'MASTER', 4 => 'PROFESSION', 5 => 'EXPERT', 6 => 'DIAMOND', 7 => 'DIRECTOR', 8 => 'PRESIDENT', 9 => 'AMBASSADOR', 10 => 'CROWN AMBASSADOR');

$c_rnk_arr = array(0, 1, 2, 3);
$c_m_arr = array(0, 25000, 50000, 100000);
$c_amt_arr = array(0, 1, 1.5, 2);

// Daily Royalty max days — iske baad user ko royalty nahi milegi
$royalty_max_days = 269;

// Check only: ?check=1 — dekhne ke liye next run pe royalty banega ya nahi (koi payment nahi)
if (!empty($_GET['check'])) {
    echo "<h3>Daily Royalty Check (Date: $date)</h3><p>Limit: $royalty_max_days din. Next time cron run karoge to inme se kisko royalty milegi:</p>";
    echo "<table border='1' cellpadding='6'><tr><th>UID</th><th>Login ID</th><th>Rank</th><th>Daily Amt</th><th>Total days</th><th>Today paid?</th><th>Next run pe</th></tr>";
    $q = mysqli_query($link, 'SELECT uid, login_id, royalty FROM user WHERE status = 0 AND topup > 0 AND royalty >= 1 ORDER BY royalty DESC, uid ASC');
    $will = 0;
    $already = 0;
    while ($u = mysqli_fetch_object($q)) {
        $uid = (int) $u->uid;
        $rlevel = (int) $u->royalty;
        $daily_amt = isset($amt_arr2[$rlevel]) ? $amt_arr2[$rlevel] : 0;
        $tot = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM income_royalty WHERE uid='$uid' AND type=2"));
        $total_days = $tot ? (int) $tot[0] : 0;
        $r = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM income_royalty WHERE uid='$uid' AND type=2 AND DATE(datetime)='$date'"));
        $paid_today = $r ? (int) $r[0] : 0;
        if ($total_days >= $royalty_max_days)
            $next_run = 'Nahi (269 din limit)';
        elseif ($paid_today)
            $next_run = 'Nahi (aaj ho chuka)';
        else
            $next_run = 'Haan milega';
        if (!$paid_today && $total_days < $royalty_max_days)
            $will++;
        if ($paid_today)
            $already++;
        $rn = isset($rank_names[$rlevel]) ? $rank_names[$rlevel] : $rlevel;
        echo "<tr><td>$uid</td><td>" . htmlspecialchars($u->login_id) . "</td><td>$rn</td><td>\$$daily_amt</td><td>$total_days / $royalty_max_days</td><td>" . ($paid_today ? 'Haan' : 'Nahi') . "</td><td>$next_run</td></tr>";
    }
    echo "</table><p><b>Summary:</b> Next run pe $will user ko royalty milegi. Aaj already paid: $already users. (Max $royalty_max_days din tak.)</p>";
    echo '<br/>Check complete. Close this tab.';
    exit;
}

// Debug specific users (no inserts): ?debug=TB366214,TB394067
if (!empty($_GET['debug'])) {
    $raw = trim((string)$_GET['debug']);
    $ids = array_values(array_filter(array_map('trim', preg_split('/[,\s]+/', $raw))));
    $ids = array_slice($ids, 0, 50);
    echo "<h3>Reward/Royalty Debug (Date: $date)</h3>";
    echo "<p><b>Note:</b> Is cron me reward/royalty rank checks <code>user.teamb</code> use karti hain. Pehle <code>cron_tb.php</code> run hona chahiye (complete). Weekend (Sat/Sun) pe ye cron die karta hai.</p>";
    if (empty($ids)) {
        echo "<p>No login_ids provided. Use: <code>?debug=TB366214,TB394067</code></p>";
        exit;
    }
    echo "<table border='1' cellpadding='6'><tr><th>Login ID</th><th>UID</th><th>Status</th><th>Topup</th><th>Reward</th><th>Royalty</th><th>TeamB (u.teamb)</th><th>Max leg</th><th>Other leg</th><th>Directs ok for next?</th><th>Next Reward level ok?</th><th>Next Royalty level ok?</th></tr>";
    foreach ($ids as $login_id) {
        $login_id_esc = mysqli_real_escape_string($link, $login_id);
        $u = mysqli_fetch_object(mysqli_query($link, "SELECT uid, login_id, status, topup, reward, royalty, teamb FROM user WHERE login_id='$login_id_esc' LIMIT 1"));
        if (!$u) {
            echo "<tr><td>" . htmlspecialchars($login_id) . "</td><td colspan='11'>User not found</td></tr>";
            continue;
        }
        $uid = (int)$u->uid;
        $reward = (int)$u->reward;
        $royalty = (int)$u->royalty;
        $teamb = (float)$u->teamb;

        $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb) DESC LIMIT 0,1"))->amount;
        $max = ($max) ? (float)$max : 0;
        $other = ($teamb) ? ($teamb - $max) : 0;
        if ($other < 0) $other = 0;

        $next_reward = min(15, $reward + 1);
        $rw_need = isset($tb_arr[$next_reward]) ? ($tb_arr[$next_reward] * 0.5) : 0;
        $next_reward_ok = ($reward < 15 && $rw_need > 0 && $rw_need <= $max && $rw_need <= $other) ? 'YES' : 'NO';

        $next_royalty = min(10, $royalty + 1);
        $ro_need = isset($tb_arr2[$next_royalty]) ? ($tb_arr2[$next_royalty] * 0.5) : 0;
        $cdn = ($next_royalty == 1) ? 5 : 2;
        $check_d = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '" . $uid . "' AND status = 0 AND topup > 0 AND royalty >= '" . ($next_royalty - 1) . "'"));
        $directs_ok = ($check_d >= $cdn) ? ('YES (' . $check_d . '/' . $cdn . ')') : ('NO (' . $check_d . '/' . $cdn . ')');
        $next_royalty_ok = ($royalty < 10 && $ro_need > 0 && $ro_need <= $max && $ro_need <= $other && $check_d >= $cdn) ? 'YES' : 'NO';

        echo "<tr>";
        echo "<td>" . htmlspecialchars($u->login_id) . "</td>";
        echo "<td>$uid</td>";
        echo "<td>" . ((int)$u->status === 0 ? '0' : htmlspecialchars((string)$u->status)) . "</td>";
        echo "<td>" . htmlspecialchars((string)$u->topup) . "</td>";
        echo "<td>$reward</td>";
        echo "<td>$royalty</td>";
        echo "<td>" . htmlspecialchars((string)$teamb) . "</td>";
        echo "<td>" . htmlspecialchars((string)$max) . "</td>";
        echo "<td>" . htmlspecialchars((string)$other) . "</td>";
        echo "<td>$directs_ok</td>";
        echo "<td>$next_reward_ok</td>";
        echo "<td>$next_royalty_ok</td>";
        echo "</tr>";
    }
    echo "</table><p>Debug complete. Close this tab.</p>";
    exit;
}

// $result = mysqli_query($link, "SELECT * FROM user WHERE topup > 0 AND reward <= 15");
$result = mysqli_query($link, '
    SELECT DISTINCT u.* 
    FROM user u
    INNER JOIN investments i ON i.uid = u.uid
    WHERE u.topup > 0 
      AND i.ipid <= 4 AND (u.reward < 15 OR royalty < 10)
');
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $reward = $row->reward;
    $royalty = $row->royalty;
    $time_floor = floor((strtotime(date('c')) - strtotime($row->datetime)) / (60 * 60 * 24));

    $i = $reward;
    // echo $i.'<br>';
    // die;
    while ($i < 15) {
        $i++;
        $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb) DESC LIMIT 0,1"))->amount;
        $max = ($max) ? $max : 0;
        // $max2 = @mysqli_fetch_object(mysqli_query($link, "SELECT SUM(teamb2) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb2) DESC LIMIT 1,9999999999"))->amount;
        $max2 = $row->teamb;
        // echo "new".$max2.'<br>';
        $max2 = ($max2) ? $max2 - $max : 0;

        echo $uid . '====' . $max . '=====' . $max2 . '======' . $tb_arr[$i] * 0.5 . '<br>';

        if ($reward < $i && ($tb_arr[$i] * 0.5) <= $max && ($tb_arr[$i] * 0.5) <= $max2) {
            $reward++;
            mysqli_query($link, "UPDATE user SET reward = '" . $reward . "' WHERE uid = '$uid'");
            $new_amount = $amt_arr[$reward];
            royalty_insert($uid, $new_amount, $reward, 0);
        } else {
            break;
        }
    }

    $i = $royalty;
    while ($i < 10) {
        $i++;
        $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb) DESC LIMIT 0,1"))->amount;
        $max = ($max) ? $max : 0;
        // $max2 = @mysqli_fetch_object(mysqli_query($link, "SELECT SUM(teamb2) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb2) DESC LIMIT 1,9999999999"))->amount;
        $max2 = $row->teamb;

        $max2 = ($max2) ? $max2 - $max : 0;
        $cdn = ($i == 1) ? 5 : 2;

        // Bronze: 5 direct referrals | Silver+: 2 direct referrals with previous rank (e.g. Two Bronze for Silver)
        $check_d = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '" . $uid . "' AND status = 0 AND topup > 0 AND royalty >= '" . ($i - 1) . "'"));

        echo $uid . '====' . $max . '=====' . $max2 . '<br>';

        if ($royalty < $i && ($tb_arr2[$i] * 0.5) <= $max && ($tb_arr2[$i] * 0.5) <= $max2 && $check_d >= $cdn) {
            $royalty++;
            mysqli_query($link, "UPDATE user SET royalty = '" . $royalty . "' WHERE uid = '$uid'");
            // Daily Royalty paid in daily block below (plan: daily income per rank)
        } else {
            break;
        }
    }
}

// Daily Royalty: pay amt_arr2[royalty] once per day when cron runs (plan: Daily Royalty)
$royalty_users = mysqli_query($link, 'SELECT uid, royalty FROM user WHERE status = 0 AND topup > 0 AND royalty >= 1');
while ($ur = mysqli_fetch_object($royalty_users)) {
    $uid = (int) $ur->uid;
    $rlevel = (int) $ur->royalty;
    if ($rlevel < 1 || $rlevel > 10)
        continue;
    // Safety: Daily payout tabhi do jab user abhi bhi same rank conditions meet kare.
    // Otherwise koi aur legacy cron / manual update se royalty=1 set hone par bhi payment nahi hogi.
    if (!is_current_royalty_rank_eligible($link, $uid, $rlevel, $tb_arr2)) {
        // Optional: auto-reset rank to 0 to stop future attempts
        mysqli_query($link, "UPDATE user SET royalty = 0 WHERE uid = '$uid'");
        continue;
    }
    $daily_amt = $amt_arr2[$rlevel];
    if ($daily_amt <= 0)
        continue;
    $res = mysqli_query($link, "SELECT COUNT(*) FROM income_royalty WHERE uid = '$uid' AND type = 2 AND DATE(datetime) = '$date'");
    $row = mysqli_fetch_row($res);
    $already = $row ? (int) $row[0] : 0;
    if ($already > 0)
        continue;
    // 269 din ka limit: total type=2 count check karo
    $tot = mysqli_fetch_row(mysqli_query($link, "SELECT COUNT(*) FROM income_royalty WHERE uid = '$uid' AND type = 2"));
    $total_royalty_days = $tot ? (int) $tot[0] : 0;
    if ($total_royalty_days >= $royalty_max_days)
        continue;
    mysqli_query($link, "UPDATE user SET wallet = wallet + '$daily_amt' WHERE uid = '$uid'");
    mysqli_query($link, "INSERT INTO income_royalty (uid, amount, datetime, level, type, tamt, tid, days) VALUES ('$uid', '$daily_amt', '" . date('c') . "', '$rlevel', 2, 0, 0, 1)");
}

/**
 * Re-check current royalty rank eligibility (not next rank).
 * Conditions same spirit as rank-up:
 * - 50% strong leg + 50% weak leg based on user.teamb (team business)
 * - Direct referral requirement: Rank-1 needs 5, Rank>=2 needs 2
 * - Directs must be active (status=0, topup>0) and have royalty >= (rlevel-1)
 */
function is_current_royalty_rank_eligible($link, $uid, $rlevel, $tb_arr2)
{
    $uid = (int) $uid;
    $rlevel = (int) $rlevel;
    if ($rlevel < 1 || $rlevel > 10) {
        return false;
    }

    $u = mysqli_fetch_object(mysqli_query($link, "SELECT teamb FROM user WHERE uid = '$uid' LIMIT 1"));
    if (!$u) {
        return false;
    }
    $teamb = (float) $u->teamb;

    $max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb) AS amount FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb) DESC LIMIT 0,1"))->amount;
    $max = ($max) ? (float) $max : 0.0;
    $other = $teamb - $max;
    if ($other < 0) {
        $other = 0.0;
    }

    $need = isset($tb_arr2[$rlevel]) ? ((float) $tb_arr2[$rlevel] * 0.5) : 0.0;
    if ($need <= 0) {
        return false;
    }

    $cdn = ($rlevel === 1) ? 5 : 2;
    $check_d = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '" . $uid . "' AND status = 0 AND topup > 0 AND royalty >= '" . ($rlevel - 1) . "'"));

    return ($need <= $max && $need <= $other && $check_d >= $cdn);
}

/*if($date == date('Y-m-d')){
    $i = 0;
    $tamt = get_sum('investments', 'amount', "statusc = 0 AND ipid <= 4")*0.3;
    while ($i < 3 && $tamt) {
        $i++;
        $tid = my_num_rows(my_query("SELECT uid FROM user WHERE status = 0 AND topup > 0 AND royalty >= '".$c_rnk_arr[$i]."'"));

        if ($tamt > 0 && $tid > 0) {
            $rs = my_query("SELECT uid FROM user WHERE status = 0 AND topup > 0 AND royalty >= '".$c_rnk_arr[$i]."'");
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
}*/

function royalty_insert($uid, $new_amount, $l, $type = 0, $n = 0, $tamt = 0)
{
    // $new_amount = check_2x($uid, $new_amount);

    if ($type == 2 && $new_amount > 0) {
        $check = my_num_rows(my_query("SELECT uid FROM income_royalty WHERE uid='" . $uid . "' AND type = '" . $type . "' AND level = '" . $l . "'"));
        if (!$check) {
            $days = $check + 1;
            my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='" . $uid . "'");
            my_query("INSERT INTO `income_royalty` (`uid`, `amount`, `datetime`, `level`, `type`, tamt, tid, days) VALUES ('" . $uid . "', '" . $new_amount . "', '" . date('c') . "', '" . $l . "', '" . $type . "', '" . ($tamt) . "', '" . $n . "', '" . $days . "')");
        }
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

if ($date == date('Y-m-d')) {
    // my_query("UPDATE investments SET statusc=1 WHERE statusc=0");
    // my_query("UPDATE income_binary SET statusc=1 WHERE statusc=0");
}
echo '<br/> Closing complete. Please close this browser.';
