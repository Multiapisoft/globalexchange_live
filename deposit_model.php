<?php include 'lib/config.php';
user();
$uid = $_SESSION['userid'];
$user = get_user_details($uid);
$status = 0;
$_msg = "Transaction failed.";
$c = '0x0b4a5779f753d452fee55577800803a3c2304bab';
$hash = isset($_POST['transactionHash']) ? tres($_POST['transactionHash']) : (isset($_POST['blockHash']) ? tres($_POST['blockHash']) : tres($_POST['hash']));
$check = my_fetch_object(my_query("SELECT * FROM `deposit_block` WHERE `txid`='$hash' AND status = 0"));
if($check){
    $_status_json = @file_get_contents("https://api.bscscan.com/api?module=transaction&action=gettxreceiptstatus&txhash=".$hash."&apikey=YKN8UP7QCPE27F8AF7VVFZIRSKNVF84KZY");
    $_status_arr = json_decode($_status_json);
    $status = (isset($_status_arr->result->status) && $_status_arr->result->status == 1) ? $_status_arr->result->status : 0;
    
    if($status){
        //my_query("UPDATE `deposit_block` SET status = 1 WHERE `recid`='".$check->recid."'");
        //my_query("UPDATE `user` SET `wallet_topup`=`wallet_topup`+'".$check->amount."' WHERE `uid`='".$check->uid."'");
        //$_msg = "Success - thank you for add wallet fund.";
        $_msg = "Success - thank you for add wallet fund.";
    }
}

echo json_encode(array('Success' => $status, 'Message' => $_msg));die;
?>