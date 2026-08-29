<?php include_once '../lib/config.php';
user();
$wallet_field_arr = get_wallet_field();
$from_uid = $_SESSION['userid'];
$user = get_user_details($from_uid);
$min = 10;
$max = 50000;

if (isset($_POST)) {
    $login_id = isset($_POST['login_id']) ? tres($_POST['login_id']) : '';
    $amount = tres($_POST['amount']);
    $type = isset($_POST['type']) ? tres($_POST['type']) : 0;
    $remark = tres($_POST['remark']);

    $wallet_field = 'wallet';
    $wallet_field2 = 'wallet_topup';

    $wallet = get_wallet($from_uid, $wallet_field);
    echo $wallet;
    echo "</br>";
    
    $otp = isset($_POST['otp']) ? tres($_POST['otp']) : '';

    $uid = isset($_POST['login_id']) ? registeredUserId($login_id) : $from_uid;
    if ($uid == 0) {
        setMessage('Invalid user id.', 'error');
    } elseif (checkDecimal($amount) == 0) {
        setMessage('Invalid amount.', 'error');
    } elseif ($user->topup <= 0) {
        setMessage('Please topup your account.', 'error');
    } elseif ($wallet < $amount) {
        setMessage('Insufficient fund.', 'error');
    } elseif ($amount < $min) {
        setMessage('Minimum fund transfer ' . $min . '.', 'error');
    } elseif ($amount > $max) {
        setMessage('Miximum fund transfer ' . $max . '.', 'error');
    } /*elseif ($amount % $min) {
        setMessage('Request Multiple of ' . $min . '.', 'error');
    }*/ elseif (!verify_email_otp($otp, 'transfer', $user->email, $user)) {
        setMessage('Invalid OTP.','error');
    } else {
        my_query("UPDATE user SET $wallet_field=$wallet_field-'" . $amount . "' WHERE uid='" . $from_uid . "'");
        $fee = $amount*0.0;
        $netamount = $amount - $fee;
        if($type == 1){
            //$netamount = $netamount*90;
        }

        my_query("UPDATE user SET $wallet_field2=$wallet_field2+'$netamount' WHERE uid='" . $uid . "'");
        my_query("INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, `datetime`, `type`, `remark`, `fee`) VALUES ('" . $uid . "', '" . $from_uid . "', '" . $amount . "', '" . date('c') . "', '" . $type . "', '" . $remark . "', '" . $fee . "')");
        setMessage('Fund transfer successfully.', 'success');
    }
}
redirect('./fund_transfer3.php');
?>