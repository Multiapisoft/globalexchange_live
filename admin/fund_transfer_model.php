<?php include_once '../lib/config.php';
admin();
$wallet_field_arr = get_wallet_field();

if(isset($_POST)){
    $login_id = tres($_POST['login_id']);
    $amount = tres($_POST['amount']);
    $type = tres($_POST['type']);
    $remark = tres($_POST['remark']);
    $wallet_field = $wallet_field_arr[$type];
    
    $uid = registeredUserId($login_id);
    if($uid==0){
        setMessage('Invalid user id.', 'error');
    }
    elseif(checkDecimal($amount)==0){
        setMessage('Invalid amount.', 'error');
    }
    /*elseif($type==1 && checkAPIBalance($amount)==0){
        setMessage('Please recharge your api.', 'error');
    }*/
    else{
        my_query( "UPDATE user SET $wallet_field=$wallet_field+'$amount' WHERE uid='".$uid."'");
        my_query( "INSERT INTO `fund_transfer` (`uid`, `from_uid`, `amount`, `datetime`, `type`, `remark`) VALUES ('".$uid."', 0, '".$amount."', '".date('c')."', '".$type."', '".$remark."')");
        setMessage('Fund transfer successfully.', 'success');
    }
}
redirect('./fund_transfer.php');
?>