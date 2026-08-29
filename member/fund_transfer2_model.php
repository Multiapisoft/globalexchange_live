<?php include_once '../lib/config.php';
user();
$wallet_field_arr = get_wallet_field();
$from_uid = $_SESSION['userid'];
$user = get_user_details($from_uid);
$from_user = $user;
$min = 10;
$max = 200;

if (isset($_POST)) {
    // echo "<pre>";
// print_r($_POST);
// die();
    $receiver_id = isset($_POST['receiver_id']) ? tres($_POST['receiver_id']) : '';
    $amount = tres($_POST['amount']);
    $type = isset($_POST['type']) ? tres($_POST['type']) : 0;
    $remark = $_POST['remark'];
    $wallet_field = 'wallet';
    $wallet_field2 = 'wallet_topup';
    $wallet = get_wallet($from_uid, $wallet_field2);
    $wallet_game = get_wallet($from_uid, 'wallet_admin');
    $wallet = $wallet + $wallet_game;

    $uid = isset($_POST['receiver_id']) ? registeredUserId($receiver_id) : $from_uid;
    $user = get_user_details($uid);
    //$user->mobile = '7409302327';
    $check = ibc($user->mobile);
    //print_r($check);

    $otp = isset($_POST['otp']) ? tres($_POST['otp']) : '';


    if (strpos($receiver_id, "BN") === 0) {
        $receiver_id = substr($receiver_id, 2);
    }
    $receiver_id = (int) $receiver_id;

    // echo $receiver_id . "<br>";
    

    $relation = check_user_relation($from_uid, $receiver_id);

    // echo $relation;
    // exit();
    if ($uid == 0) {
        setMessage('Invalid user id.', 'error');
    } elseif (checkDecimal($amount) == 0) {
        setMessage('Invalid amount.', 'error');
        // } elseif (!$check->status) {
        //     setMessage('Invalid user not found in game.', 'error');
    } elseif ($user->topup <= 0) {
        setMessage('Please topup your account.', 'error');
    } elseif ($wallet < $amount) {
        setMessage('Insufficient fund.', 'error');
    } elseif ($amount < $min) {
        setMessage('Minimum fund transfer ' . $min . '.', 'error');
    } 
    // elseif ($amount > $max) {
    //     setMessage('Miximum fund transfer ' . $max . '.', 'error');
    // }
     /*elseif ($amount % $min) {
      setMessage('Request Multiple of ' . $min . '.', 'error');
  }*/ elseif (!verify_email_otp($otp, 'transfer', $from_user->email, $from_user)) {
        setMessage('Invalid OTP.', 'error');
    } elseif ($relation == 'none' || $relation == 'self') {
        setMessage('Transfer money team and upline only.', 'error');
    } else {
        $amount2 = $amount;
        if ($wallet_game > 0) {
            $amount2 = $amount - 10;
        }
        // my_query("UPDATE user SET $wallet_field=$wallet_field-'" . $amount2 . "', wallet_admin = 0 WHERE uid='" . $from_uid . "'");
        my_query("UPDATE user SET $wallet_field2=$wallet_field2-'" . $amount . "' WHERE uid='" . $from_uid . "'");
        $fee = 0;
        $netamount = $amount - $fee;

        $d = ibc($user->mobile, $netamount * 85);

        //print_r($d);die;
        my_query("UPDATE user SET $wallet_field2=$wallet_field2+'$netamount' WHERE uid='" . $uid . "'");
        my_query("INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, `datetime`, `type`, `remark`, `fee`) VALUES ('" . $uid . "', '" . $from_uid . "', '" . $amount . "', '" . date('c') . "', '" . $type . "', '" . $remark . "', '" . $fee . "')");
        setMessage('Fund transfer successfully.', 'success');
    }
}
redirect('./fund_transfer.php');

function ibc($phone, $amount = 0)
{
    $url = 'https://game.ibcgroup.live/api/webapi/';
    $url .= ($amount > 0) ? 'transfer_by_rbc' : 'check_user_by_rbc';

    $data = array(
        "phone" => $phone,
        "amount" => $amount,
        "apikey" => "xxxxxxxx"
    );

    $encodedData = json_encode($data);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type:application/json'
    ));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedData);
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result);
}
?>