<?php include '../lib/config.php';
include '../lib/coinpayments.php';
admin();
$recid = isset($_GET['recid']) ? $_GET['recid'] : 0;
$withdraw_arr = array();
$is_night = 1;
$_k = 0;
$_k++;

if(isset($recid)){
    $remark = 'AUTO W';
    $check = my_fetch_object(my_query("SELECT * FROM withdrawal_block WHERE recid='".$recid."' AND status=0"));
    $type = $check->type;
    $account = $check->withdrawal_address;
    $wallet_field = 'wallet';
    
    if(isset($_GET['cancel']) && $check){
        my_query("UPDATE withdrawal_block SET approved_datetime='".date('c')."', status=2 WHERE recid='".$recid."' AND status=0");
        my_query("UPDATE user SET $wallet_field=$wallet_field+'".$check->amount."' WHERE uid='".$check->uid."'");
    }elseif($check){
        if (!$is_night && $check->type == 'USDT') {
            $_price = round($check->net_amount, 2);
            $withdraw_arr[$_k][] = $check->recid;
            $withdraw_arr[$_k][] = $check->withdrawal_address;
            $withdraw_arr[$_k][] = $_price;
            
            my_query("UPDATE withdrawal_block SET amount_coin = '" . $_price . "' WHERE recid='" . $recid . "'");
            
            //coinpayments_new_withdrawal($withdraw_arr, $check->type);
        }
        my_query("UPDATE withdrawal_block SET approved_datetime='".date('c')."', status=1 WHERE recid='".$recid."' AND status=0");
        
        // Store fee in walfare_fund
        my_query("INSERT INTO walfare_fund (uid, amount, datetime) VALUES ('" . $check->uid . "', '" . $check->fee . "', '" . date('c') . "')");
    }
    
    my_query("UPDATE withdrawal_block SET remark='".$remark."' WHERE recid='".$recid."'");
    setMessage('Successfully approved.', 'alert alert-success');
}
redirect('./report_withdrawal_block.php');
?>