<?php session_start();
include_once '../lib/config.php';
include_once '../lib/function_lib.php';
admin();

$recid = isset($_GET['recid']) ? $_GET['recid'] : 0;

if(isset($recid) && $recid){
    $check = my_fetch_object(my_query("SELECT * FROM deposit_block WHERE recid='".$recid."' AND status=0"));
    $amount = $check->net_amount;
    $wallet_field = 'wallet_topup';
    
    if(isset($_GET['cancel']) && $check){
        my_query("UPDATE deposit_block SET status=2 WHERE recid='".$recid."' AND status=0");
    }elseif($check){
        my_query("UPDATE deposit_block SET status=1 WHERE recid='".$recid."' AND status=0");
        /*************************************/
        my_query("UPDATE user SET $wallet_field=$wallet_field+'".$check->net_amount."' WHERE uid='".$check->uid."'");
        /*************************************/
    }
    setMessage('Successfully approved.', 'alert alert-success');
}
redirect('./report_deposit_block.php');
?>