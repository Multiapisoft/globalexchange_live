<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];
$wallet_field_arr = get_wallet_field();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(-1);

// Check and add exchange_pair column
$check_pair = my_query("SHOW COLUMNS FROM `investments` LIKE 'exchange_pair'");
if (mysqli_num_rows($check_pair) == 0) {
    $query_pair = "ALTER TABLE `investments` ADD `exchange_pair` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Trading pair like BTC/USDT' AFTER `invest_hour`;";
    my_query($query_pair);
}

// Check and add exchange_coin column
$check_coin = my_query("SHOW COLUMNS FROM `investments` LIKE 'exchange_coin'");
if (mysqli_num_rows($check_coin) == 0) {
    $query_coin = "ALTER TABLE `investments` ADD `exchange_coin` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Selected cryptocurrency' AFTER `exchange_pair`;";
    my_query($query_coin);
}

$recid = isset($_POST['recid']) ? (int) tres($_POST['recid']) : 0;
$iRow = my_fetch_object(my_query("SELECT * FROM investments_plan WHERE recid='" . $recid . "'"));
if (isset($_POST) && $iRow) {
    $user = get_user_details($uid);
    $uid2 = isset($_POST['uid']) ? (int) tres($_POST['uid']) : $uid;
    $user_2 = get_user_details($uid2);

    $type = (int) tres($_POST['type']);
    $hour = isset($_POST['hour']) ? (int) tres($_POST['hour']) : 0;
    //$wallet_field = $wallet_field_arr[0];
    $wallet_field = 'wallet_topup';
    $wallet = $user->$wallet_field;

    $min = $iRow->amount_from;
    $max = $iRow->amount_to;
    $mul = $min;
    $mul = 1;
    $recid = $iRow->recid;
    $amount = tres($_POST['amount']);
    $time = tres($_POST['time']) ?? 0;

    // if (!$time && $recid == 2) {
    //     setMessage('Invalid time period.', 'error');
    // } else
    if ((int) $recid === 1) {
        $alreadyActivated = my_num_rows(my_query("SELECT recid FROM investments WHERE uid = '" . (int) $uid . "' AND ipid = 1 LIMIT 1")) > 0;
        if ($alreadyActivated) {
            setMessage('Bot Activation Account already purchased. It can be bought only once.', 'error');
            redirect('./trade.php');
            exit;
        }
    }
    if (in_array($recid, [2, 3], true)) {
        $hasBotActivation = my_num_rows(my_query("SELECT recid FROM investments WHERE uid = '" . (int) $uid . "' AND ipid = 1 LIMIT 1")) > 0;
        if (!$hasBotActivation) {
            setMessage('Please buy Bot Activation Account first to unlock trading packages.', 'error');
            redirect('./trade.php');
            exit;
        }
    }
    if (checkDecimal($amount) == 0) {
        setMessage('Invalid amount.', 'error');
    } elseif (!in_array($type, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10))) {
        setMessage('Invalid amount.', 'error');
    } elseif ($amount < 0) {
        setMessage('Invalid amount.', 'error');
    } elseif ($amount < $min || $amount > $max) {
        setMessage('Invest min ' . SITE_CURRENCY . '' . $min . ' and max ' . SITE_CURRENCY . '' . $max . ' multiple of ' . $mul . '.', 'error');
    } elseif ($amount % $mul) {
        setMessage('Invest min ' . SITE_CURRENCY . '' . $min . ' and max ' . SITE_CURRENCY . '' . $max . ' multiple of ' . $mul . '.', 'error');
    } elseif ($wallet < $amount) {
        setMessage('Insufficient funds to cover this investment.', 'error');
    } else {
        my_query("UPDATE user SET $wallet_field=$wallet_field-'" . ($amount) . "' WHERE uid='" . $uid . "'");

        $_uid = $uid;
        $uid = isset($_POST['uid']) ? (int) tres($_POST['uid']) : $uid;

        $camt = round($amount / B_RATE_, 2);
        $bamt = $amount;

        $iamount = $amount;
        $_package = ($recid < $user_2->package) ? $user_2->package : $recid;

        if ($user_2->topup > 0) {
            my_query("UPDATE user SET package='" . $_package . "', invest_count = invest_count + 1, topup=topup+'$amount' WHERE uid='$uid'");
        } else {
            $topupa = ($recid == 1) ? $amount : $amount;
            $tkna = ($recid == 1) ? 0 : 0;
            my_query("UPDATE user SET package='" . $_package . "', invest_count = invest_count + 1, topup=topup+'$topupa', wallet_token = $tkna, topup_datetime='" . date('c') . "' WHERE uid='$uid' ");
        }


        // Get exchange pair and coin from form (only for Self-Trading and Bot Trading)
        $exchange_pair = '';
        $exchange_coin = '';
        
        if (in_array($recid, [2, 3])) { // Self-Trading and Bot Trading
            $exchange_pair = isset($_POST['exchange_pair']) ? tres($_POST['exchange_pair']) : '';
            $exchange_coin = isset($_POST['exchange_coin']) ? tres($_POST['exchange_coin']) : '';
        }
        
        my_query("INSERT INTO investments (uid, amount, amount2, ipid, datetime, type, uid2, trade_status, amount_coin, bonus, invest_hour, exchange_pair, exchange_coin) VALUES ('$uid', '$amount', '$amount', '$recid', '" . date('c') . "', '$type', '$_uid', 1, '" . $camt . "', '" . $bamt . "', '" . $time . "', '" . $exchange_pair . "', '" . $exchange_coin . "')");

        // Bot Activation (ipid=1): no ROI / MLM income per Business Plan.
        // Trading packages (Silver/Gold): income via Trading ROI + Level ROI only.
       
        /*************************/
        if($recid == 4 && 0){
            $top = get_top_level_uids2($uid, 30);
    
            $level_amount = array(10, 5, 3, 2, 0.5, 0.5, 0.5, 0.5, 0.5, 0.5, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3, 0.3);
    
            $i = 0;
            $_d = 0;
            $level = count($top);
            if($level>30){$level=30;}
            if($level>0){
                while($i<$level){
                    $value = $top[$i];
                    if($i<15){$j=$i;}else{$j=15;}
                    $percentage = $level_amount[$j];
                    //$new_amount = $percentage * $amount;
                    $new_amount = $percentage;
                    $user2 = get_user_details($value);
                    //$new_amount = check_3x($value, $new_amount);
                    $check = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '$value' AND topup > 0"));
                    $check2 = my_num_rows(my_query("SELECT uid FROM investments WHERE uid = '$value' AND ipid = 4"));

                    if($i >= 10){
                        $_d = 6;
                    }
                    elseif($i >= 4){
                        $_d = 6;
                    }
                    elseif($i >= 3){
                        $_d = 4;
                    }
                    elseif($i >= 2){
                        $_d = 2;
                    }

                    if($check2 && $user2->topup > 0 && $new_amount > 0 && ($check >= $_d || $check >= 10)){
                        my_query("UPDATE user SET wallet= wallet+'$new_amount' WHERE uid='".$value."'");
    
                        /*if($i==0){
                            my_query("INSERT INTO `income_direct` (`uid`, `from_uid`, `amount`, `datetime`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."', '".$recid."', '".$iamount."')");
                        }
                        else{*/
                            my_query("INSERT INTO `income_level` (`uid`, `from_uid`, `amount`, `datetime`, `level`, ipid, iamount) VALUES ('" .$value ."','".$uid."','".$new_amount."','".date('c')."','".($i+1)."', '".$recid."', '".$iamount."')");
                        //}
                    }
                    $i++;
                }
            }
        }
        /*************************/

        setMessage('Success - thank you for invest.', 'success');
    }
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
// Redirect back to trade page after successful investment
redirect('./trade.php');
