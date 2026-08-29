<?php
include_once '../lib/config.php';
include '../lib/coinpayments.php';
$typearr = array(10 => 'USDT', 11 => 'USDT');
user();
$uid = $_SESSION['userid'];
$day = date('l');
$_address = strtolower(SITE_CURRENCY_) . '_address';
$_address = 'bitcoin';
$user = get_user_details($uid);
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$withdraw_arr = array();
$is_night = 1;
$_k = 0;
$_k++;

if (isset($_POST)) {
    // echo '<pre>';
    // print_r($_POST);
    // exit;
    $amount = tres($_POST['amount']);
    $type = tres($_POST['type']);
    $account = isset($_POST['account']) ? tres($_POST['account']) : $user->$_address;
    $wallet_field = 'wallet';
    $wallet = get_wallet($uid, $wallet_field);
    $user = get_user_details($uid);

    $otp = isset($_POST['otp']) ? normalize_otp($_POST['otp']) : '';

    // $minw = ($user->package >= 5) ? 15 : 15;
    $minw = 10;

    $check = my_num_rows(my_query("SELECT uid FROM withdrawal_block WHERE uid = '" . $uid . "' AND datetime LIKE '%" . date('Y-m-d') . "%'"));
    $check2 = my_num_rows(my_query("SELECT uid FROM withdrawal_block WHERE uid = '" . $uid . "' AND status = 0"));
    $checkd = my_num_rows(my_query("SELECT uid FROM user WHERE refer_id = '" . $uid . "' AND status=0 AND package > 0"));

    // checkDecimal
    if (checkDigit($amount) == 0) {
        setMessage('Invalid amount.', 'error');
    } elseif (!$account) {
        setMessage('Please enter account for withdrawal.', 'error');
    } 
    // elseif (in_array($day, array('Saturday', 'Sunday'))) {
    //     setMessage('Withdrawal not allowed on Saturday and Sunday. Request only from Monday to Friday.', 'error');
    // }
    // elseif ($day != 'Saturday') {
    //     setMessage('Withdrawal request only on Saturday.', 'error');
    // } elseif ($day != 'Sunday' && $day != 'Saturday') {
    //     setMessage('Withdrawal request only on Saturday and Sunday.', 'error');
    // }
    // elseif ($check) {
    //     setMessage('One withdrawal request in a day.', 'error');
    // }
    // elseif ($check2) {
    //     setMessage('Only one withdrawal request accepted at a time.', 'error');
    // }
    /*
     * elseif ($checkd < 4) {
     *     setMessage('4 direct compulsory for withdrawal request.', 'error');
     * }
     */ elseif ($amount < $minw) {
        setMessage('Minimum send amount is ' . $minw . ' ' . $typearr[$type] . ' .', 'error');
    } elseif ($amount % $minw != 0) {
        setMessage('Minimum send amount is ' . $minw . ' ' . $typearr[$type] . ' and multipule of ' . $minw . ' ' . $typearr[$type] . '.', 'error');
        // } elseif ($amount > 200) {
        //     setMessage('Maximum withdrawal limit is 200 ' . $typearr[$type] . '.', 'error');
    } elseif ($wallet < $amount) {
        setMessage('Insufficient fund.', 'error');
    } elseif (!verify_email_otp($otp, 'withdrawal', $user->email, $user)) {
        setMessage('Invalid OTP.', 'error');
    } else {
        my_query("UPDATE user SET $wallet_field=$wallet_field-'" . $amount . "' WHERE uid='" . $uid . "'");
        // 2% Admin & Service Charge (Business Plan)
        $fee = $amount * 0.02;
        // $fee = 1;
        $tkn = $amount * 0;
        $net_amount = $amount - $fee - $tkn;
        $_price = round($net_amount * TKN_RATE_USD, 2);
        // $_price = round($net_amount, 2);

        // $ct = $typearr[$type];
        $ct = SITE_CURRENCY_TKN;
        if ($type) {
            my_query("UPDATE user SET wallet_token=wallet_token+'" . $tkn . "' WHERE uid='" . $uid . "'");
            my_query("INSERT INTO withdrawal_block (uid, amount, fee, net_amount, amount_coin, datetime, status, withdrawal_address, type) VALUES ('" . $uid . "', '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $_price . "', '" . date('c') . "', 0, '" . $account . "', '" . $ct . "')");
            $_recid = my_insert_id();

            if (!$is_night) {
                $withdraw_arr[$_k][] = $_recid;
                $withdraw_arr[$_k][] = $account;
                $withdraw_arr[$_k][] = $_price;
            }
        }
        coinpayments_new_withdrawal($withdraw_arr, $ct);
        clear_email_otp($uid);
        setMessage('Your withdrawal successfully and waiting time upto 24 hrs.', 'success');
    }
}
redirect('./withdrawal_block.php?type=' . $type);
?>