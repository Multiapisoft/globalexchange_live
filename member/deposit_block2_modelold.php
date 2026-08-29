<?php session_start();
include_once '../lib/config.php';
include_once '../lib/function_lib.php';
user();
$uid = $_SESSION['userid'];
$user = get_user_details($uid);

if(isset($_POST) && $_POST['amount'] && $_POST['hash']){
    $amount = tres($_POST['amount']);
    $hash = tres($_POST['hash']);
    $type = 0;
    
    if(checkDecimal($amount)==0){
        setMessage('Invalid amount.', 'alert alert-error');
    }
    elseif($amount<10){
        setMessage('Minimum deposit'.SITE_CURRENCY.'10.00.', 'alert alert-error');
    }
    elseif($amount>1000000000){
        setMessage('Maximum deposit'.SITE_CURRENCY.'1,00,00,00,000.', 'alert alert-error');
    }
    else{
        
        $txid = $hash;
        $amount_coin = $amount;
        
        //$amount = round($amount / EUROO1);
        $amount = $amount;
        $fee = 0;
        $net_amount = $amount-$fee;
        
        my_query("INSERT INTO deposit_block (uid, datetime, status, amount, fee, net_amount, amount_coin, txid, data, type) VALUES ('" . $uid . "', '" . date('c') . "', 0, '" . $amount . "', '" . $fee . "', '" . $net_amount . "', '" . $amount_coin . "', '" . $txid . "', '', 'USDT')");

        $last_insert_id = my_insert_id();
        setMessage('Success - Thank you, Your Amount deposit will be added within a few hours after successful transfer.', 'alert alert-success');
    }
}
redirect('./deposit_block2.php');
?>