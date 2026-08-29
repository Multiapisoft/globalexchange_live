<?php include_once '../lib/config.php';

// $result = mysqli_query($link, "SELECT uid, topup FROM user WHERE topup > 0");
// Include both Bot Trading (ipid=3) and Bot Subscription (ipid=4) for matching income
$result = mysqli_query($link, "
    SELECT DISTINCT u.* 
    FROM user u
    INNER JOIN investments i ON i.uid = u.uid
    WHERE u.topup > 0 
      AND i.ipid = 4
");
while ($row = mysqli_fetch_object($result)) {
    $uid = $row->uid;
    $amount_left = 0;
    $amount_right = 0;
    
    $direct_count = my_num_rows(mysqli_query($link, "SELECT uid FROM user WHERE topup > 0 AND refer_id='" . $uid . "'"));
    $capping_amount = $row->topup >= 15000 ? 25000 : 12500;
    // $capping = $row->topup;
    $capping = 50000000;

    $childs = get_single_dimensional(get_child_levels($uid));
    $luid = @mysqli_fetch_object(mysqli_query($link, "SELECT `uid` FROM user WHERE refer_id = '" . $uid . "' AND status = 0 ORDER BY (teamb2) DESC LIMIT 0,1"))->uid;

    
    $childs_left = get_single_dimensional(get_child_levels($luid));
    $childs_right = array_diff($childs, $childs_left);
    $amount_left = get_amount($childs_left);
    $amount_right = get_amount($childs_right);
    
    $ld = get_direct_($uid, $childs_left);
    $rd = get_direct_($uid, $childs_right);

    if ($amount_left != 0 || $amount_right != 0) {
        $amount_left = $amount_left + get_carry_amount($uid, 'left_carry');
        $amount_right = $amount_right + get_carry_amount($uid, 'right_carry');
    }

    $min_three = count($childs_left) + count($childs_right);

    if ($amount_left != 0 && $amount_right == 0) {
        $is_insert = mysqli_query($link, "INSERT INTO income_binary (uid, datetime, pair_left, pair_right, matching, left_carry, right_carry) VALUES ($uid, '" . date('c') . "', $amount_left, $amount_right, 0, $amount_left, $amount_right)");
    } elseif ($amount_left == 0 && $amount_right != 0) {
        $is_insert = mysqli_query($link, "INSERT INTO income_binary (uid, datetime, pair_left, pair_right, matching, left_carry, right_carry) VALUES ($uid, '" . date('c') . "', $amount_left, $amount_right, 0 , $amount_left, $amount_right)");
    } /*elseif ($ld == 0 || $rd == 0) {
        $is_insert = mysqli_query($link, "INSERT INTO income_binary (uid, datetime, pair_left, pair_right, matching, left_carry, right_carry) VALUES ($uid, '" . date('c') . "', $amount_left, $amount_right, 0 , $amount_left, $amount_right)");
    }*/
    /*elseif($min_three<3 && $amount_left != 0 && $amount_right != 0){
        $is_insert = mysqli_query($link, "INSERT INTO income_binary (uid, datetime, pair_left, pair_right, matching, left_carry, right_carry) VALUES ($uid, '".date('c')."', $amount_left, $amount_right, 0 , $amount_left, $amount_right)");
    }*/ elseif ($amount_left != 0 && $amount_right != 0) {

        if ($amount_left > $amount_right) {
            $matching_amount = $amount_right >= 50000000 ? 50000000 : $amount_right;
            $left_carry = $amount_left - $amount_right;
            $right_carry = 0;
        } elseif ($amount_right == $amount_left) {
            $matching_amount = $amount_left >= 50000000 ? 50000000 : $amount_left;
            $right_carry = 0;
            $left_carry = 0;
        } elseif ($amount_right > $amount_left) {
            $matching_amount = $amount_left >= 50000000 ? 50000000 : $amount_left;
            $right_carry = $amount_right - $amount_left;
            $left_carry = 0;
        } else {
            $matching_amount = 0;
            $left_carry = 0;
            $right_carry = 0;
        }

        $user4 = my_fetch_object(my_query("SELECT * FROM user WHERE uid='" . $uid . "'"));
        /*$per = 6/30;
        if($user4->package >= 3){
            $per = (4+$user4->package)/30;
        }*/
        $per = 10;

        //$balance = $matching_amount * 125 * $per / 100;
        $balance = $matching_amount * $per;

        $balance = $balance >= $capping ? $capping : $balance;

        /*$tinv = get_sum('investments', 'amount', "uid='$uid'")*4;
        $tinc = get_sum('income_direct', 'amount', "uid='$uid'")+get_sum('income_binary', 'amount', "uid='$uid'")+get_sum('income_growth', 'amount', "uid='$uid'")+get_sum('income_royalty', 'amount', "uid='$uid'")+get_sum('income_level', 'amount', "uid='" . $uid . "' AND type = 0");
        $tincn = $tinc + $balance;

        if($tinv < $tincn){
            $balance = $tinv - $tinc;
        }*/

        if ($balance < 0) {
            $balance = 0;
        }

        $is_insert = mysqli_query($link, "INSERT INTO income_binary (uid, amount, datetime, pair_left, pair_right, matching, left_carry, right_carry) VALUES ($uid, '$balance', '" . date('c') . "', $amount_left, $amount_right, $matching_amount, $left_carry, $right_carry)");
        if ($is_insert) {
            mysqli_query($link, "UPDATE user SET wallet = wallet + $balance WHERE uid = '$uid'");
        }
    }
}

function get_amount($id_array)
{
    global $link;
    $amount = 0;
    if (!empty($id_array)) {
        $uid_in = implode(" , ", $id_array);
        //$amount = mysqli_fetch_object(mysqli_query($link, "SELECT sum(topup) as amount FROM user WHERE uid IN ( $uid_in ) AND binary_status=0 AND topup!=0"))->amount;
        // $amount = my_num_rows(mysqli_query($link, "SELECT uid FROM investments WHERE uid IN ( $uid_in ) AND binary_status=0 AND ipid IN (3, 4)"));
        $amount = my_num_rows(mysqli_query($link, "SELECT uid FROM investments WHERE uid IN ( $uid_in ) AND binary_status=0 AND ipid = 4"));
    }
    return $amount;
}

function get_direct_($uid, $id_array)
{
    global $link;
    $amount = 0;
    if (!empty($id_array)) {
        $uid_in = implode(" , ", $id_array);
        $amount = my_num_rows(mysqli_query($link, "SELECT uid FROM user WHERE uid IN ( $uid_in ) AND refer_id = '" . $uid . "' AND topup!=0"));
    }
    return $amount;
}

function get_carry_amount($uid, $position)
{
    global $link;
    $amount = 0;
    $rs = mysqli_query($link, "SELECT $position as amount FROM income_binary WHERE recid = (SELECT MAX(recid) FROM income_binary WHERE uid='$uid')");
    if (mysqli_num_rows($rs)) {
        $amount = $amount + mysqli_fetch_object($rs)->amount;
    }
    return $amount;
}

mysqli_query($link, "UPDATE investments SET binary_status=1 WHERE binary_status=0");
echo "<br/> Closing complete. Please close this browser.";
?>