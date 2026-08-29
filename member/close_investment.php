<?php
include_once '../lib/config.php';

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    setMessage('Please login first', 'danger');
    redirect('./login.php');
    exit;
}

// Check if form is submitted
if (isset($_POST['submit-button']) && isset($_POST['investment_id'])) {
    $investment_id = intval($_POST['investment_id']);
    $uid = $_SESSION['userid'];

    // Fetch investment details
    $inv = my_query("SELECT * FROM investments WHERE recid='$investment_id' AND uid='$uid' LIMIT 1");
    $row = mysqli_fetch_object($inv);

    if (!$row) {
        setMessage('Invalid investment!', 'danger');
        redirect('./trade.php');
        exit;
    }

    if ($row->is_closed == 1) {
        setMessage('Investment is already closed!', 'warning');
        redirect('./trade.php');
        exit;
    }

    // Check if investment time has expired
    $investTime = strtotime($row->datetime);
    $cycleHours = (int)$row->invest_hour;
    $elapsed = time() - $investTime;

    if ($elapsed < ($cycleHours * 3600)) {
        setMessage('Investment time has not completed yet. Please wait.', 'warning');
        redirect('./trade.php');
        exit;
    }

    // Get plan details
    $plan = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $row->ipid . "'"));
    $min_per = $plan->percentage;
    $max_per = $plan->percentage_to;

    // Calculate Random ROI %
    $percentage = round((mt_rand($min_per * 1000, $max_per * 1000) / 1000) / 720, 2) * $row->invest_hour;

    $amount = ($row->amount * $percentage) / 100;
    $iamount = $row->amount;

    if ($amount > 0) {
        // 1️⃣ Add to income_growth
        my_query("INSERT INTO `income_growth` (`uid`, `iid`, `days`, `amount`, `datetime`, `percentage`, `iamount`) 
                  VALUES ('$uid', '$investment_id', 1, '$amount', '" . date('c') . "', '$percentage', '$iamount')");

        // 2️⃣ Update investment as closed
        my_query("UPDATE investments SET is_closed=1 WHERE recid='$investment_id'");

        // 3️⃣ Credit to user wallet
        my_query("UPDATE user SET wallet=wallet+'$amount' WHERE uid='$uid'");

        // Add invested amount to wallet_topup
        my_query("UPDATE user SET wallet_topup=wallet_topup+'$iamount' WHERE uid='$uid'");

        // 4️⃣ Give level ROI
        incentives($uid, $amount, $iamount);
        
        setMessage('Investment Closed Successfully. ROI + Level income credited.', 'success');
    } else {
        // Just update investment as closed
        my_query("UPDATE investments SET is_closed=1 WHERE recid='$investment_id'");
        setMessage('Investment closed successfully!', 'success');
    }
} else {
    setMessage('Invalid request.', 'danger');
}

// Redirect back to trade page
redirect('./trade.php');
exit;

// ==================== HELPER FUNCTIONS ====================

function incentives($uid, $amount, $pamt)
{
    $top = get_top_level_uids2($uid, 10);

    // Business Plan Level ROI % of downline Trading ROI
    $level_amount = array(
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
    );

    $i = 0;
    if (!is_array($top))
        $top = [];
    $level = count($top);

    if ($level > 10) {
        $level = 10;
    }

    if ($level > 0) {
        while ($i < $level) {
            $value = $top[$i];
            $percentage = ($level_amount[$i]);
            $new_amount = $percentage * $amount;
            $user2 = get_user_details($value);
            $new_amount = check_capping($value, $new_amount);

            $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id='" . $value . "' AND status = 0 AND topup > 0"));
            $level_no = $i + 1;
            $required_directs = get_level_roi_required_directs($level_no);

            if ($user2->topup > 0 && $new_amount > 0 && ($check >= $required_directs)) {
                my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='" . $value . "'");
                my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `iamount`, `datetime`, `level`, type) VALUES ('" . $value . "','" . $uid . "','" . $new_amount . "','" . $pamt . "','" . date('c') . "','" . $level_no . "', 2)");
            }
            $i++;
        }
    }
}

/* Get top level uids */
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
?>

