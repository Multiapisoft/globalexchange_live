<?php include_once '../lib/config.php';
user();
$uid = $_SESSION['userid'];
$wallet_field_arr = get_wallet_field();
$per_arr = array(0,0,0,0,0,0,.2,.2,.2,.02,.01,.01,.02,.02,.02);
$user = my_fetch_object(my_query("SELECT * FROM `user` WHERE `uid`='".$uid."'"));

if(isset($_POST) && SITE_WORKING_STATUS == 0){
    $amount = tres($_POST['amount']);
    $operator = tres($_POST['operator']);
    $number = tres($_POST['number']);
    $type = isset($_POST['type']) ? tres($_POST['type']) : 0;
    $balance_field = $wallet_field_arr[1];
    $balance = get_wallet($uid, $balance_field);
    $wallet_field = $wallet_field_arr[0];
    $api_balance = get_api_balance();
    $p1 = isset($_POST['p1']) ? tres($_POST['p1']) : '';
    $p2 = isset($_POST['p2']) ? tres($_POST['p2']) : '';
    
    if(checkDigit($amount)==0){
        setMessage('Invalid amount.', 'alert alert-error');
    }
    elseif($balance<$amount){
        setMessage('Insufficient recharge balance.', 'alert alert-error');
    }
    elseif($api_balance<$amount){
        setMessage('Your Recharge FAILED, try again later.', 'alert alert-error');
    }
    else{
        $percentage = 0;
        $recharge = my_query("INSERT INTO `recharge` (`uid`, `number`, `operator`, `amount`, `datetime`, `status`, `transaction_id`, `type`, `other`, `other2`) VALUES ('".$uid."', '".$number."', '".$operator."', '".$amount."', '".date('c')."', 'PENDING', 0, '".$type."', '".$p1."', '".$p2."')");
        if($recharge){
            $recharge_id = my_fetch_object(my_query("SELECT recid FROM `recharge` WHERE uid='".$uid."' AND number='".$number."' AND operator='".$operator."' AND amount='$amount' ORDER BY recid DESC LIMIT 1"))->recid;
            $balance_deduct = my_query("UPDATE `user` SET $balance_field=$balance_field-'$amount' WHERE uid='".$uid."'");
            //$balance_deduct = 1;
            if($balance_deduct){
                $data = recharge($type, $operator, $number, $amount, $recharge_id, $p1, $p2);
                if(strtolower($data[1]) == strtolower('SUCCESS') || strtolower($data[1]) == strtolower('PENDING')){
                    $user_income = ($amount * $percentage)/100;
                    my_query("UPDATE `recharge` SET `status`='".$data[1]."', `transaction_id`='".$data[0]."', `user_income`='".$user_income."' WHERE recid='".$recharge_id."'");
                    my_query("UPDATE user SET $balance_field=$balance_field+'".$user_income."' WHERE uid='".$uid."'");

                    setMessage('Your Recharge completed successfully.', 'alert alert-success');
                }
                elseif(strtolower($data[1]) == strtolower('FAILURE') || strtolower($data[1]) == strtolower('FAILED')){
                    my_query("UPDATE `user` SET $balance_field=$balance_field+'".$amount."' WHERE uid='".$uid."'");
                    my_query("UPDATE `recharge` SET `status`='".$data[1]."', `transaction_id`='".$data[0]."' WHERE recid='".$recharge_id."'");
                    setMessage('Your Recharge FAILED, try again later.', 'alert alert-error');
                }
                else{
                    setMessage('Your Recharge completed successfully.', 'alert alert-success');
                }
            }
            $api_balance = get_api_balance();
            $user_balance = get_wallet($uid, $balance_field);
            my_query("UPDATE `recharge` SET `api_balance`='".$api_balance."', `balance`='".$user_balance."' WHERE `recid`='".$recharge_id."'");
        }
    }
}
redirect('./recharge.php');
?>