<?php include 'lib/config.php';
include 'lib/moralis.php';

$rs = my_query("SELECT * FROM deposit_block WHERE status = 0");
while ($row = my_fetch_object($rs)) {
    $txid = isset($row->txid) ? $row->txid : '';
    $address = isset($row->address) ? $row->address : '';
    $date = date('Y-m-d', strtotime($row->datetime));
    $data = check_transaction_details($txid, $address, $date);
    $status = $data['status'];
    $amount = $data['amount'];
    echo '<pre>';print_r($data);echo '</pre><br>';
    if($status && $amount == $row->amount_coin){
        my_query("UPDATE `deposit_block` SET status = 1 WHERE `recid`='".$row->recid."'");
        my_query("UPDATE `user` SET `wallet_topup`=`wallet_topup`+'".$row->amount."' WHERE `uid`='".$row->uid."'");
    }
}

echo 'success';die;
?>