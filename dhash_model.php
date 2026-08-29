<?php include 'lib/config.php';
user();
$uid = $_SESSION['userid'];
$user = get_user_details($uid);
$_msg = "Error";

$account = tres($_POST['account']);
$hash = tres($_POST['hash']);
$amount = tres($_POST['amount']);
$amount_coin = $amount;
$amount = $amount/TKN_RATE_USD;
$fee = 0;
$net_amount = $amount - $fee;

if($hash && $account && $amount){
    $check = my_num_rows(my_query("SELECT `uid` FROM `deposit_block` WHERE `txid`='$hash'"));
    if(!$check){
        $data_json = '';
        $currency = SITE_CURRENCY_TKN;
        my_query("INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, address, txid, data, type) VALUES ('" . $uid . "', '" . date('Y-m-d H:i:s') . "', 0, '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $amount_coin . "', '" . $account . "', '" . $hash . "', '" . $data_json . "', '" . $currency . "')");
        
        if($account && !$user->bnb_address){
            my_query("UPDATE `user` SET `bnb_address`='".$account."' WHERE `uid`='".$uid."'");
        }
        $_msg = "Success";
    }
}
echo $_msg;die;
?>