<?php include_once '../lib/config.php';
$wallet_field_arr = get_wallet_field();
$mobile = isset($_GET['phone']) ? tres($_GET['phone']) : '';
$rs = my_query("SELECT * FROM user WHERE mobile = '".$mobile."'");
$err = array('status' => false, 'msg' => 'Invailid user');

if ($user && my_num_rows($rs) == 1) {
    $amount = isset($_GET['amount']) ? tres($_GET['amount']) : 0;
    $apikey = isset($_GET['apikey']) ? tres($_GET['apikey']) : '';
    if($amount > 0 && $apikey == 'yyyyyyyyyyy'){
        $user = my_fetch_object($rs);
        $uid = $user->uid;
        $user = get_user_details($uid);
        $min = 1;
        $max = 200;
        $type = 0;
        $remark = 'From Game';
        $wallet_field = 'wallet';
        
        $amount = $amount/85;
        
        if (checkDecimal($amount) == 0) {
            $err['msg'] = 'Invalid amount.';
        } /*elseif ($user->topup <= 0) {
            $err['msg'] = 'Please topup your account.';
        }*/ elseif ($amount < $min) {
            $err['msg'] = 'Minimum fund transfer ' . $min . '.';
        } elseif ($amount > $max) {
            $err['msg'] = 'Miximum fund transfer ' . $max . '.';
        } else {
            my_query("UPDATE user SET $wallet_field=$wallet_field+'" . $amount . "' WHERE uid='" . $uid . "'");
            $fee = 0;
            $netamount = $amount - $fee;
            
            my_query("INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, `datetime`, `type`, `remark`, `fee`) VALUES ('" . $uid . "', '" . $uid . "', '" . $amount . "', '" . date('c') . "', '" . $type . "', '" . $remark . "', '" . $fee . "')");
            $err['msg'] = 'Fund transfer successfully.';
            $err['status'] = 1;
        }
    }
}
echo json_encode($err);
?>